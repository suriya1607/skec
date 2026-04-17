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

    protected $casts = [
        'published_at' => 'datetime',
        'file_size'    => 'integer',
        'total_pages'  => 'integer',
        'view_count'   => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(NoteCategory::class, 'category_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
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
}
