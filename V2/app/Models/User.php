<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'hotel_id',
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
            'password' => 'hashed',
        ];
    }

    public function hotel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** Редактор локации: правит контент только своего отеля. Без привязанного отеля прав не имеет. */
    public function isEditor(): bool
    {
        return $this->role === 'editor' && $this->hotel_id !== null;
    }

    /** Администратор: всё. Роль по умолчанию — admin (так было до появления ролей). */
    public function isAdmin(): bool
    {
        return $this->role !== 'editor';
    }

    /**
     * Доступ в админку. Публичной регистрации нет — пользователей создаёт администратор
     * в разделе «Пользователи» (или командой `php artisan make:filament-user`).
     * Если задан ADMIN_EMAILS (через запятую), администраторов пускаем только из списка;
     * редактор локации входит, если ему назначен отель.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->role === 'editor') {
            return $this->hotel_id !== null;
        }

        $allowed = array_filter(array_map('trim', explode(',', (string) env('ADMIN_EMAILS', ''))));

        return $allowed === [] || in_array(strtolower($this->email), array_map('strtolower', $allowed), true);
    }
}
