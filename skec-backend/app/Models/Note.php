<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'subject_id',
        'file_name',
        'file_path',
        'file_size',
        'file_hash',
        'mime_type',
        'total_pages',
        'status',
        'published_at',
        'uploaded_by',
        'view_count',
    ];

    protected $hidden = [
        'file_path',
    ];

    protected $appends = [
        'file_size_formatted',
        'file_url',
        'categories',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'file_size'    => 'integer',
        'total_pages'  => 'integer',
        'view_count'   => 'integer',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(NoteSubject::class, 'subject_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    // Accessor: resolve comma-separated category_id into array of category objects
    public function getCategoriesAttribute()
    {
        if (empty($this->category_id)) {
            return [];
        }

        $ids = array_map('intval', array_filter(explode(',', $this->category_id)));

        if (empty($ids)) {
            return [];
        }

        return NoteCategory::whereIn('id', $ids)->get();
    }

    // Helper: get array of category IDs
    public function getCategoryIdsArray(): array
    {
        if (empty($this->category_id)) {
            return [];
        }
        return array_map('intval', array_filter(explode(',', $this->category_id)));
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Scope: filter notes that have a specific category ID in their comma-separated list
    public function scopeHasCategory($query, $categoryId)
    {
        return $query->whereRaw('FIND_IN_SET(?, category_id)', [$categoryId]);
    }

    // Accessors
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
    public function getFileUrlAttribute(): string
    {
        return route('notes.view', ['id' => $this->id]);
    }
}
