<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description', 'group', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value, cast by type, from cache first.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("settings_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) return $default;
            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value and clear its cache entry.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
        Cache::forget("settings_{$key}");
    }

    /**
     * Get all settings in a group.
     */
    public static function getGroup(string $group): array
    {
        $settings = static::where('group', $group)->get();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = static::castValue($setting->value, $setting->type);
        }
        return $result;
    }

    /**
     * Cast value string to appropriate PHP type.
     */
    public static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) return null;
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'float'   => (float) $value,
            default   => (string) $value,
        };
    }

    /**
     * Get typed value attribute dynamically.
     */
    public function getTypedValueAttribute(): mixed
    {
        return static::castValue($this->value, $this->type);
    }
}
