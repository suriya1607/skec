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

    public function course()
    {
        return $this->belongsTo(NoteCategory::class, 'course_id');
    }
}
