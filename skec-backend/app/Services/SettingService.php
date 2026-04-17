<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function getAllSettings(): array
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get();
        $result = [];
        foreach ($settings as $setting) {
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

    public function updateSettings(array $data): void
    {
        foreach ($data as $key => $value) {
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
