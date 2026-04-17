<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = [
        'user_id',
        'note_id',
        'session_id',
        'action',
        'page_number',
        'ip_address',
        'duration_seconds',
    ];

    protected $casts = [
        'page_number'      => 'integer',
        'duration_seconds' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function session()
    {
        return $this->belongsTo(UserSession::class, 'session_id');
    }
}
