<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'message',
        'type',
        'target_category_ids',
        'sent_count',
    ];

    protected $casts = [
        'sent_count' => 'integer',
    ];

    protected $appends = ['target_categories'];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Accessor: resolve comma-separated target_category_ids into category objects
    public function getTargetCategoriesAttribute()
    {
        if (empty($this->target_category_ids)) {
            return [];
        }
        $ids = array_map('intval', array_filter(explode(',', $this->target_category_ids)));
        if (empty($ids)) return [];
        return NoteCategory::whereIn('id', $ids)->get(['id', 'name', 'color']);
    }

    // Helper: get array of target category IDs
    public function getTargetCategoryIdsArray(): array
    {
        if (empty($this->target_category_ids)) {
            return [];
        }
        return array_map('intval', array_filter(explode(',', $this->target_category_ids)));
    }
}
