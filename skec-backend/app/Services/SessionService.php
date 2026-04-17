<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class SessionService
{
    public function createSession(User $user, Request $request): string
    {
        $rawToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $deviceType = $this->detectDeviceType($request->userAgent());

        UserSession::create([
            'user_id' => $user->id,
            'session_token' => $tokenHash,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $deviceType,
            'last_activity' => now(),
            'is_active' => true,
        ]);

        return $rawToken;
    }

    public function validateSession(User $user, string $tokenHash): bool
    {
        $session = UserSession::where('user_id', $user->id)
            ->where('session_token', $tokenHash)
            ->where('is_active', true)
            ->first();

        if (!$session)
            return false;

        $timeoutMinutes = Setting::get('session_timeout_minutes', 60);
        if ($session->last_activity->addMinutes($timeoutMinutes)->isPast()) {
            $session->update(['is_active' => false]);
            return false;
        }

        $session->update(['last_activity' => now()]);
        return true;
    }

    public function terminateOtherSessions(User $user, string $currentTokenHash): void
    {
        UserSession::where('user_id', $user->id)
            ->where('session_token', '!=', $currentTokenHash)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    public function terminateSession(string $tokenHash): void
    {
        UserSession::where('session_token', $tokenHash)
            ->update(['is_active' => false]);
    }

    public function terminateAllUserSessions(User $user): void
    {
        UserSession::where('user_id', $user->id)
            ->update(['is_active' => false]);
    }

    public function getActiveSessions(User $user): Collection
    {
        return UserSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    public function terminateOldestIfExceeded(User $user): void
    {
        $maxSessions = Setting::get('max_sessions_per_user', 1);
        $activeSessions = UserSession::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_activity', 'asc')
            ->get();

        $excessSessions = $activeSessions->count() - $maxSessions + 1;

        if ($excessSessions > 0) {
            $sessionsToDeactivate = $activeSessions->take($excessSessions);

            foreach ($sessionsToDeactivate as $session) {
                $session->update(['is_active' => false]);
            }
        }
    }

    private function detectDeviceType(?string $userAgent): string
    {
        if (!$userAgent)
            return 'desktop';

        $ua = strtolower($userAgent);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }
        return 'desktop';
    }
}
