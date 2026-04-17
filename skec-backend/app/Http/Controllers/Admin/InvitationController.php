<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteStudentRequest;
use App\Models\Invitation;
use App\Services\InvitationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    use ApiResponse;

    public function __construct(private InvitationService $invitationService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Invitation::with('inviter')->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            match ($request->status) {
                'pending' => $query->pending(),
                'expired' => $query->expired(),
                'used'    => $query->used(),
                default   => null,
            };
        }

        $invitations = $query->paginate(15);
        return $this->paginatedResponse($invitations);
    }

    public function store(InviteStudentRequest $request): JsonResponse
    {
        $invitation = $this->invitationService->create($request->email, $request->user()->id);
        $this->invitationService->sendMail($invitation);

        return $this->created([
            'invitation'       => $invitation,
            'registration_link'=> env('FRONTEND_URL') . '/register?token=' . $invitation->token,
        ], 'Invitation sent successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $invitation = Invitation::findOrFail($id);
        $invitation->delete();
        return $this->noContent();
    }

    public function resend(int $id): JsonResponse
    {
        $invitation = Invitation::findOrFail($id);

        if ($invitation->isUsed()) {
            return $this->error('Cannot resend a used invitation.', 'invitation_used', 409);
        }

        $this->invitationService->resend($invitation);

        return $this->success([
            'invitation'       => $invitation->fresh(),
            'registration_link'=> env('FRONTEND_URL') . '/register?token=' . $invitation->token,
        ], 'Invitation resent');
    }
}
