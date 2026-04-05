<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Access\Services\PanelAccessService;
use App\Models\Concerns\HasMediaUrl;
use App\Services\Media\MediaUploadService;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasMediaUrl;
    use HasRoles;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'is_active',
        'email_verified_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        return $this->isActive()
            && app(PanelAccessService::class)->canAccessAdminPanel($this);
    }

    protected static function booted(): void
    {
        static::forceDeleted(function (User $user): void {
            app(MediaUploadService::class)->delete($user->avatar);
        });
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->avatar);
    }

    public function isCoreAdministrator(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canManageUsers(): bool
    {
        return $this->can('manage_users') || $this->can('view_any_users');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('akses')
            ->logOnly(['name', 'email', 'avatar', 'is_active', 'email_verified_at', 'last_login_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
