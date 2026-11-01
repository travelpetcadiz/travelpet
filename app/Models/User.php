<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'activo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Igual que los valores por defecto de la tabla, para que un modelo recién creado los tenga
    protected $attributes = [
        'role' => 'usuario',
        'activo' => true,
    ];

    public const ROLES = [
        'admin' => 'Administrador',
        'editor' => 'Editor',
        'usuario' => 'Usuario',
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
            'activo' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'editor'], true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->activo && $this->isStaff();
    }

    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }

    public function viaje(): BelongsToMany
    {
        return $this->belongsToMany(Sitio::class, 'viaje')->withPivot('estado')->withTimestamps();
    }
}
