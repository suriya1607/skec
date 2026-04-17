<?php

namespace App\Http\Controllers;

use App\Exceptions\InvitationExpiredException;
use App\Exceptions\InvitationNotFoundException;
use App\Exceptions\InvitationUsedException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterFromInviteRequest;
use App\Models\User;
use App\Services\InvitationService;
use App\Services\SessionService;
use App\Services\SettingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private InvitationService $invitationService,
        private SessionService $sessionService,
        private SettingService $settingService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid email or password.', 'invalid_credentials', 401);
        }

        if (!$user->isActive()) {
            return $this->error('Your account has been deactivated.', 'account_inactive', 403);
        }

        // Terminate oldest session if max exceeded
        $this->sessionService->terminateOldestIfExceeded($user);

        // Create Sanctum token
        $sanctumToken = $user->createToken('auth_token')->plainTextToken;

        // Create session record
        $rawSessionToken = $this->sessionService->createSession($user, $request);

        // Update login info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return $this->success([
            'user'          => $user,
            'token'         => $sanctumToken,
            'session_token' => $rawSessionToken,
            'expires_at'    => now()->addMinutes(60)->toIso8601String(),
            'settings'      => $this->settingService->getPublicSettings(),
        ], 'Login successful');
    }

    public function validateInvitation(string $token): JsonResponse
    {
        try {
            $invitation = $this->invitationService->validate($token);
            return $this->success([
                'email'      => $invitation->email,
                'expires_at' => $invitation->expires_at,
                'valid'      => true,
            ], 'Invitation is valid');
        } catch (InvitationExpiredException $e) {
            return $this->error($e->getMessage(), 'invitation_expired', 410);
        } catch (InvitationUsedException $e) {
            return $this->error($e->getMessage(), 'invitation_used', 409);
        } catch (InvitationNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    public function registerFromInvitation(RegisterFromInviteRequest $request): JsonResponse
    {
        try {
            $invitation = $this->invitationService->validate($request->token);
        } catch (InvitationExpiredException $e) {
            return $this->error($e->getMessage(), 'invitation_expired', 410);
        } catch (InvitationUsedException $e) {
            return $this->error($e->getMessage(), 'invitation_used', 409);
        } catch (InvitationNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $invitation->email,
            'password' => Hash::make($request->password),
            'role'     => 'student',
            'status'   => 'active',
        ]);

        $this->invitationService->markUsed($invitation, $user);

        // Auto-login
        $this->sessionService->terminateOldestIfExceeded($user);
        $sanctumToken    = $user->createToken('auth_token')->plainTextToken;
        $rawSessionToken = $this->sessionService->createSession($user, $request);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return $this->created([
            'user'          => $user,
            'token'         => $sanctumToken,
            'session_token' => $rawSessionToken,
            'expires_at'    => now()->addMinutes(60)->toIso8601String(),
            'settings'      => $this->settingService->getPublicSettings(),
        ], 'Registration successful');
    }

    public function logout(Request $request): JsonResponse
    {
        $rawToken = $request->header('X-Session-Token');
        if ($rawToken) {
            $tokenHash = hash('sha256', $rawToken);
            $this->sessionService->terminateSession($tokenHash);
        }

        $request->user()->currentAccessToken()->delete();

        return $this->noContent();
    }

    public function me(Request $request): JsonResponse
    {
        $user     = $request->user()->load([]);
        $sessions = $this->sessionService->getActiveSessions($user);

        return $this->success([
            'user'            => $user,
            'active_sessions' => $sessions->count(),
        ]);
    }
}
