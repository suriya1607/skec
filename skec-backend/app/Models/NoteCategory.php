<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'sort_order',
        'is_active',
        'open_in_browser',
        'is_free',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
        'open_in_browser' => 'boolean',
        'is_free'         => 'boolean',
    ];

    // Get notes that include this category in their comma-separated category_id
    public function getNotesCountAttribute(): int
    {
        return Note::hasCategory($this->id)->count();
    }

    public function getPublishedNotesCountAttribute(): int
    {
        return Note::hasCategory($this->id)->published()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
