<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, InteractsWithMedia, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_super_admin',
        'status',
        'avatar',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'photo_url',
        'is_super_admin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_super_admin' => 'boolean',
        ];
    }

    // Accessor for is_super_admin
    public function getIsSuperAdminAttribute(): bool
    {
        if (isset($this->attributes['is_super_admin']) && $this->attributes['is_super_admin']) {
            return true;
        }
        if ($this->role === 'super_admin') {
            return true;
        }
        // Fallback: If id is 1 and role is admin, consider primary admin as super admin
        if ($this->id === 1 && $this->role === 'admin') {
            return true;
        }
        return false;
    }

    // Relationships
    public function invitationsSent()
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'uploaded_by');
    }

    public function userSessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }

    public function profile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('student_photo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(240)
            ->height(240)
            ->nonQueued();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('student_photo') ?: null;
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']) || $this->is_super_admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeSuperAdmins($query)
    {
        return $query->where('role', 'admin')->where('is_super_admin', true);
    }
}
