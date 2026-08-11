<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Get all settings. Restricts sensitive security settings (like batch_delete_key)
     * to Super Administrators only.
     */
    public function getAllSettings(?User $user = null): array
    {
        $settings = Setting::orderBy('group')->orderBy('id')->get();
        $result = [];
        $isSuperAdmin = $user && $user->isSuperAdmin();

        foreach ($settings as $setting) {
            // Restrict batch_delete_key to Super Admin only
            if ($setting->key === 'batch_delete_key' && !$isSuperAdmin) {
                continue;
            }

            $result[$setting->key] = [
                'value'       => Setting::castValue($setting->value, $setting->type),
                'type'        => $setting->type,
                'group'       => $setting->group,
                'description' => $setting->description,
                'is_public'   => $setting->is_public,
            ];
        }
        return $result;
    }

    public function getPublicSettings(): array
    {
        $settings = Setting::where('is_public', true)->get();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = Setting::castValue($setting->value, $setting->type);
        }
        return $result;
    }

    public function updateSettings(array $data, ?User $user = null): void
    {
        $isSuperAdmin = $user && $user->isSuperAdmin();

        foreach ($data as $key => $value) {
            // Ignore non-super admin attempts to change sensitive settings
            if ($key === 'batch_delete_key' && !$isSuperAdmin) {
                continue;
            }

            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => (string) $value]);
                Cache::forget("settings_{$key}");
            }
        }
    }

    public function clearCache(): void
    {
        $settings = Setting::all();
        foreach ($settings as $setting) {
            Cache::forget("settings_{$setting->key}");
        }
    }
}
