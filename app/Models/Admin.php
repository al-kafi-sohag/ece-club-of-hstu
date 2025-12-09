<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use App\Notifications\AdminResetPasswordNotification;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use App\Traits\SendsEmailTemplates;

class Admin extends Authenticatable implements HasMedia
{
    use HasFactory, SoftDeletes, Notifiable, InteractsWithMedia, SendsEmailTemplates;

    const STATUS_ACTIVE = 1;
    const STATUS_PENDING = 0;
    const STATUS_DEACTIVE = -1;

    public $guarded = [];

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'profileImageUrl',
        'status_text',
        'status_class',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ScopeActive($query): Builder
    {
        return $query->where('status', 1);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->sendTemplatedEmail('password.reset', $this->email, [
            'user_name' => $this->name,
            'reset_link' => route('backend.auth.rp', ['token' => $token, 'email' => $this->email], true),
            'app_name' => config('app.name'),
        ]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile')
            ->singleFile();
    }

    public function getProfileImageUrl(): string
    {
        if ($this->hasMedia('profile')) {
            return $this->getFirstMediaUrl('profile');
        }

        return asset('backend/img/placeholder.svg');
    }

    public function getProfileImageUrlAttribute(): string
    {
        return $this->getProfileImageUrl();
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_DEACTIVE => 'Deactive',
            default => 'Unknown',
        };
    }

    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_ACTIVE => 'success',
            self::STATUS_DEACTIVE => 'danger',
            default => 'Unknown',
        };
    }


    public function created_admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    public function updated_admin()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function deleted_admin()
    {
        return $this->belongsTo(Admin::class, 'deleted_by');
    }
}
