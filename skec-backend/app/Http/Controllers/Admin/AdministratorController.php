<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SessionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdministratorController extends Controller
{
    use ApiResponse;

    public function __construct(private SessionService $sessionService)
    {
    }

    /**
     * List all administrator accounts (Super Admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::admins();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $admins = $query->orderBy('created_at', 'desc')->paginate(15);

        return $this->paginatedResponse($admins);
    }

    /**
     * Create a new administrator account (Super Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8'],
            'status'         => ['nullable', 'in:active,inactive'],
            'is_super_admin' => ['nullable', 'boolean'],
        ]);

        $isSuper = !empty($validated['is_super_admin']);

        $admin = User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role'           => $isSuper ? 'super_admin' : 'admin',
            'is_super_admin' => $isSuper,
            'status'         => $validated['status'] ?? 'active',
        ]);

        return $this->created($admin, 'Administrator created successfully');
    }

    /**
     * Show administrator details.
     */
    public function show(int $id): JsonResponse
    {
        $admin = User::admins()->findOrFail($id);
        return $this->success($admin);
    }

    /**
     * Update an administrator account.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $admin = User::admins()->findOrFail($id);
        $currentUser = $request->user();

        $validated = $request->validate([
            'name'           => ['sometimes', 'string', 'max:255'],
            'email'          => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password'       => ['nullable', 'string', 'min:8'],
            'status'         => ['sometimes', 'in:active,inactive'],
            'is_super_admin' => ['sometimes', 'boolean'],
        ]);

        // Prevent self-deactivation
        if ($id === $currentUser->id && isset($validated['status']) && $validated['status'] === 'inactive') {
            return $this->error('You cannot deactivate your own account.', 'self_deactivation', 422);
        }

        // Prevent self-demotion if you are super admin
        if ($id === $currentUser->id && isset($validated['is_super_admin']) && !$validated['is_super_admin']) {
            return $this->error('You cannot remove Super Admin rights from yourself.', 'self_demotion', 422);
        }

        $data = [
            'name'   => $validated['name'] ?? $admin->name,
            'email'  => $validated['email'] ?? $admin->email,
            'status' => $validated['status'] ?? $admin->status,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if (isset($validated['is_super_admin'])) {
            $data['is_super_admin'] = (bool) $validated['is_super_admin'];
            $data['role']           = $data['is_super_admin'] ? 'super_admin' : 'admin';
        }

        $admin->update($data);

        return $this->success($admin->fresh(), 'Administrator updated successfully');
    }

    /**
     * Delete an administrator account.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $admin = User::admins()->findOrFail($id);
        $currentUser = $request->user();

        if ($id === $currentUser->id) {
            return $this->error('You cannot delete your own account.', 'self_deletion', 422);
        }

        // Check if this is the last Super Admin
        if ($admin->isSuperAdmin()) {
            $superAdminCount = User::superAdmins()->count();
            if ($superAdminCount <= 1) {
                return $this->error('Cannot delete the last Super Administrator account.', 'last_super_admin', 422);
            }
        }

        $this->sessionService->terminateAllUserSessions($admin);
        $admin->tokens()->delete();
        $admin->delete();

        return $this->noContent();
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $admin = User::admins()->findOrFail($id);
        $currentUser = $request->user();

        if ($id === $currentUser->id) {
            return $this->error('You cannot deactivate your own account.', 'self_deactivation', 422);
        }

        $newStatus = $admin->status === 'active' ? 'inactive' : 'active';
        $admin->update(['status' => $newStatus]);

        if ($newStatus === 'inactive') {
            $this->sessionService->terminateAllUserSessions($admin);
            $admin->tokens()->delete();
        }

        return $this->success($admin, "Administrator status changed to {$newStatus}");
    }
}
