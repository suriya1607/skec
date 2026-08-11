<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'reg_no',
        'father_name',
        'dob',
        'gender',
        'address',
        'community_category',
        'contact_phone',
        'qualification',
        'course_id',
        'medium_of_studying',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the primary (first) course for backward-compatibility.
     */
    public function course()
    {
        $firstId = $this->getPrimaryCourseId();
        if (!$firstId) return null;
        return NoteCategory::find($firstId);
    }

    /**
     * Get all courses as a collection (supports comma-separated course_id).
     */
    public function getCourses()
    {
        $ids = $this->getCourseIdsArray();
        if (empty($ids)) return collect();
        return NoteCategory::whereIn('id', $ids)->get();
    }

    /**
     * Return course_id as an array of integers.
     */
    public function getCourseIdsArray(): array
    {
        if (empty($this->course_id)) {
            return [];
        }
        return array_map('intval', array_filter(explode(',', (string) $this->course_id)));
    }

    /**
     * Return the primary (first) course ID as integer, or null.
     */
    public function getPrimaryCourseId(): ?int
    {
        $ids = $this->getCourseIdsArray();
        return !empty($ids) ? $ids[0] : null;
    }
}
