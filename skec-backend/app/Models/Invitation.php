<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Invitation extends Model
{
    protected $fillable = [
        'email',
        'token',
        'invited_by',
        'expires_at',
        'used_at',
        'used_by',
        'resend_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    // Relationships
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function usedByUser()
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    // Helper methods
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereNull('used_at')->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNull('used_at')->where('expires_at', '<=', now());
    }

    public function scopeUsed($query)
    {
        return $query->whereNotNull('used_at');
    }
}
