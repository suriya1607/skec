<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteSubject extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function notes()
    {
        return $this->hasMany(Note::class, 'subject_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
