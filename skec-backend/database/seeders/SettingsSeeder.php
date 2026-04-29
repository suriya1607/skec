<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── General ──────────────────────────────────────────────────────
            ['key' => 'app_name',        'value' => 'Sri Kumaran Education Centre', 'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Application name shown everywhere'],
            ['key' => 'app_tagline',     'value' => 'Your Digital Learning Hub',    'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Short tagline under app name'],
            ['key' => 'app_logo',        'value' => null,                           'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Logo image URL'],
            ['key' => 'app_favicon',     'value' => null,                           'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Favicon URL'],
            ['key' => 'contact_email',   'value' => 'admin@srikumaran.in',          'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Primary contact email'],
            ['key' => 'contact_phone',   'value' => '',                             'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Contact phone number'],
            ['key' => 'address',         'value' => '',                             'type' => 'string',  'group' => 'general', 'is_public' => true,  'description' => 'Centre address'],

            // ── Auth ──────────────────────────────────────────────────────────
            ['key' => 'invite_expiry_hours',     'value' => '48', 'type' => 'integer', 'group' => 'auth', 'is_public' => false, 'description' => 'Invitation expiry in hours'],
            ['key' => 'max_sessions_per_user',   'value' => '1',  'type' => 'integer', 'group' => 'auth', 'is_public' => false, 'description' => 'Max concurrent sessions per user'],
            ['key' => 'session_timeout_minutes', 'value' => '60', 'type' => 'integer', 'group' => 'auth', 'is_public' => true,  'description' => 'Session inactivity timeout in minutes'],

            // ── Content ────────────────────────────────────────────────────────
            ['key' => 'max_file_size_mb',    'value' => '50',              'type' => 'integer', 'group' => 'content', 'is_public' => true, 'description' => 'Maximum PDF upload size in MB'],
            ['key' => 'allowed_file_types',  'value' => 'application/pdf', 'type' => 'string',  'group' => 'content', 'is_public' => true, 'description' => 'Allowed MIME types for upload'],

            // ── Security ──────────────────────────────────────────────────────
            ['key' => 'watermark_opacity',       'value' => '0.15',            'type' => 'float',   'group' => 'security', 'is_public' => true,  'description' => 'PDF watermark opacity (0–1)'],
            ['key' => 'watermark_text_template', 'value' => '{email} | {date}','type' => 'string',  'group' => 'security', 'is_public' => true,  'description' => 'Watermark text template'],
            ['key' => 'enable_access_logs',      'value' => 'true',            'type' => 'boolean', 'group' => 'security', 'is_public' => false, 'description' => 'Enable PDF access logging'],

            // ── Landing — Hero ────────────────────────────────────────────────
            ['key' => 'hero_image', 'value' => null, 'type' => 'string', 'group' => 'landing', 'is_public' => true],
            ['key' => 'hero_badge',         'value' => 'Admissions Open — Batch 2025',     'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Badge text in hero section'],
            ['key' => 'hero_title',         'value' => 'Shaping Futures Through',           'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Hero main title (line 1)'],
            ['key' => 'hero_subtitle',      'value' => 'Excellence in Education',           'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Hero subtitle (line 2, highlighted colour)'],
            ['key' => 'hero_description',   'value' => 'Sri Kumaran Education Centre — nurturing academic excellence, character, and lifelong learning for every student.', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Hero paragraph description'],
            ['key' => 'hero_cta_primary',   'value' => 'Access Learning Portal',            'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Primary CTA button label in hero'],
            ['key' => 'hero_cta_secondary', 'value' => 'Learn More',                        'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Secondary CTA button label in hero'],

            // ── Landing — Slider ──────────────────────────────────────────────
            ['key' => 'rank_title', 'value' => 'Top Rank Holders', 'type' => 'string', 'group' => 'landing', 'is_public' => true],
            ['key' => 'slider_images',      'value' => '[]',   'type' => 'string',  'group' => 'landing', 'is_public' => true, 'description' => 'Slider images JSON [{url,caption,subcaption}]. Manage in Media tab.'],
            ['key' => 'slider_autoplay',    'value' => 'true', 'type' => 'boolean', 'group' => 'landing', 'is_public' => true, 'description' => 'Auto-play hero image slider'],
            ['key' => 'slider_interval',    'value' => '5000', 'type' => 'integer', 'group' => 'landing', 'is_public' => true, 'description' => 'Slider auto-play interval (ms)'],

            // ── Landing — Stats ───────────────────────────────────────────────
            ['key' => 'stat_students',      'value' => '500+', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Total students enrolled stat'],
            ['key' => 'stat_years',         'value' => '10+',  'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Years of excellence stat'],
            ['key' => 'stat_pass_rate',     'value' => '98%',  'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Board exam pass rate stat'],
            ['key' => 'stat_rank_1',        'value' => '50+',  'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'District Rank 1 holders count'],

            // ── Landing — About ───────────────────────────────────────────────
            ['key' => 'about_title',        'value' => 'A Legacy of Academic Excellence',   'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'About section heading'],
            ['key' => 'about_description',  'value' => 'Sri Kumaran Education Centre has been a beacon of quality education, empowering students with knowledge, skills, and values needed to thrive in the modern world.', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'About section description paragraph'],
            // ['key' => 'about_image',        'value' => null,   'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'About section image URL (upload via Media)'],
            ['key' => 'about_points',       'value' => 'Expert faculty with years of teaching experience|Personalised attention for every student|Digital learning materials and resources|Regular assessments and performance tracking', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'About bullet points separated by pipe |'],

            // ── Landing — Current Batch ───────────────────────────────────────
            ['key' => 'batch_title',        'value' => 'Current Batch 2025',                'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Current batch section title'],
            ['key' => 'batch_description',  'value' => 'Join our growing community of learners. Limited seats available.', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Batch section description'],
            ['key' => 'batch_items',        'value' => '[{"name":"Class 6\u20138","desc":"Foundation years \u2014 building core concepts and study habits","seats":"40","featured":false},{"name":"Class 9\u201310","desc":"Board exam preparation with focus on scores and concept mastery","seats":"60","featured":true},{"name":"Class 11\u201312","desc":"Higher secondary coaching for science and commerce streams","seats":"35","featured":false}]', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Batch cards JSON: [{name,desc,seats,featured}]'],

            // ── Landing — Openings ────────────────────────────────────────────
            ['key' => 'openings_title',      'value' => 'Current Openings & Enrollment',    'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Openings panel title'],
            ['key' => 'openings_description','value' => 'Secure your seat for the upcoming academic year. Reach out to our team.', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Openings panel description'],
            ['key' => 'openings_items',      'value' => '[{"title":"New Admissions","detail":"June \u2013 July 2025 enrollment open"},{"title":"Scholarship Test","detail":"Merit-based discounts available"},{"title":"Trial Classes","detail":"Free demo session for new students"}]', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Openings JSON: [{title, detail}]'],

            // ── Landing — Achievements ────────────────────────────────────────
            ['key' => 'achievements_title',       'value' => 'Milestones & Achievements',   'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Achievements section title'],
            ['key' => 'achievements_description', 'value' => 'Decades of excellence reflected in the success of our students.', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Achievements section description'],
            ['key' => 'achievements_items',       'value' => '[{"metric":"98%","title":"Board Exam Pass Rate","description":"Students consistently achieving above 85%."},{"metric":"50+","title":"District Rank Holders","description":"Over 50 district toppers across all grades."},{"metric":"500+","title":"Students Mentored","description":"Graduates who went on to pursue successful careers."},{"metric":"10+","title":"Years of Service","description":"A decade of trusted academic excellence."},{"metric":"95%","title":"Parent Satisfaction","description":"Parents trust us with their children\'s future."},{"metric":"15+","title":"Subject Specialists","description":"Dedicated experts covering every topic in depth."}]', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Achievements JSON: [{metric, title, description}]'],

            // ── Landing — Gallery ─────────────────────────────────────────────
            ['key' => 'gallery_enabled',     'value' => 'false', 'type' => 'boolean', 'group' => 'landing', 'is_public' => true, 'description' => 'Show photo gallery section'],
            ['key' => 'gallery_title',       'value' => 'Our Campus & Events',              'type' => 'string',  'group' => 'landing', 'is_public' => true, 'description' => 'Gallery section title'],
            ['key' => 'gallery_images',      'value' => '[]',    'type' => 'string',  'group' => 'landing', 'is_public' => true, 'description' => 'Gallery images JSON [{url,caption}]. Manage in Media tab.'],

            // ── Landing — Testimonials ────────────────────────────────────────
            ['key' => 'testimonials_title',  'value' => 'Voices from Our Community',        'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Testimonials section title'],
            ['key' => 'testimonials_items',  'value' => '[{"quote":"SKEC completely transformed how I approach studies. I went from average to district topper!","name":"Priya S.","batch":"Class 10, Batch 2024"},{"quote":"The teachers here genuinely care about every student. The digital notes are incredibly helpful.","name":"Arjun M.","batch":"Class 12, Batch 2023"},{"quote":"Best coaching centre in the district. My son scored 98% in boards thanks to SKEC.","name":"Meena R.","batch":"Parent"}]', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'Testimonials JSON: [{quote, name, batch}]'],

            // ── Landing — CTA ─────────────────────────────────────────────────
            ['key' => 'cta_title',           'value' => 'Ready to Begin Your Journey?',     'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'CTA section heading'],
            ['key' => 'cta_description',     'value' => 'Join hundreds of students achieving their academic goals at SKEC.', 'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'CTA section description'],
            ['key' => 'cta_primary_label',   'value' => 'Contact Us',                       'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'CTA primary button label'],
            ['key' => 'cta_secondary_label', 'value' => 'Student Login',                    'type' => 'string', 'group' => 'landing', 'is_public' => true, 'description' => 'CTA secondary button label'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✓ Settings seeded (' . count($settings) . ' entries)');
    }
}