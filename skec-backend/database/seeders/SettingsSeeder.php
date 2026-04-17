<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app_name',       'value' => 'Sri Kumaran Education Centre', 'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Application name'],
            ['key' => 'app_tagline',    'value' => 'Your Digital Learning Hub',    'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Application tagline'],
            ['key' => 'app_logo',       'value' => null,                           'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Application logo URL'],
            ['key' => 'contact_email',  'value' => 'admin@srikumaran.in',          'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Contact email'],

            // Auth
            ['key' => 'invite_expiry_hours',     'value' => '48', 'type' => 'integer', 'group' => 'auth', 'is_public' => false, 'description' => 'Invitation expiry in hours'],
            ['key' => 'max_sessions_per_user',   'value' => '1',  'type' => 'integer', 'group' => 'auth', 'is_public' => false, 'description' => 'Max concurrent sessions per user'],
            ['key' => 'session_timeout_minutes', 'value' => '60', 'type' => 'integer', 'group' => 'auth', 'is_public' => true,  'description' => 'Session inactivity timeout in minutes'],

            // Content
            ['key' => 'max_file_size_mb',    'value' => '50',               'type' => 'integer', 'group' => 'content', 'is_public' => true,  'description' => 'Maximum file upload size in MB'],
            ['key' => 'allowed_file_types',  'value' => 'application/pdf',  'type' => 'string',  'group' => 'content', 'is_public' => true,  'description' => 'Allowed MIME types for upload'],

            // Security
            ['key' => 'watermark_opacity',        'value' => '0.15',              'type' => 'float',   'group' => 'security', 'is_public' => true,  'description' => 'PDF watermark opacity (0–1)'],
            ['key' => 'watermark_text_template',  'value' => '{email} | {date}', 'type' => 'string',  'group' => 'security', 'is_public' => true,  'description' => 'Watermark text template'],
            ['key' => 'enable_access_logs',       'value' => 'true',             'type' => 'boolean', 'group' => 'security', 'is_public' => false, 'description' => 'Enable access logging'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
