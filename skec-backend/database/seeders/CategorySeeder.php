<?php

namespace Database\Seeders;

use App\Models\NoteCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Mathematics',    'color' => '#3498DB', 'icon' => 'calculator'],
            ['name' => 'Science',        'color' => '#2ECC71', 'icon' => 'beaker'],
            ['name' => 'English',        'color' => '#E74C3C', 'icon' => 'book-open'],
            ['name' => 'Social Studies', 'color' => '#F39C12', 'icon' => 'globe'],
            ['name' => 'General',        'color' => '#9B59B6', 'icon' => 'document'],
        ];

        foreach ($categories as $i => $category) {
            NoteCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name'       => $category['name'],
                    'color'      => $category['color'],
                    'icon'       => $category['icon'],
                    'sort_order' => $i,
                    'is_active'  => true,
                ]
            );
        }
    }
}
