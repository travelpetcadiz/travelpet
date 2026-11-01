<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('travelpet:admin {email?} {--nombre=} {--rol=admin}')]
#[Description('Crea un usuario administrador (o editor), o cambia el rol de uno existente')]
class CrearAdmin extends Command
{
    public function handle(): int
    {
        $rol = $this->option('rol');
        if (! array_key_exists($rol, User::ROLES)) {
            $this->error('Rol no válido. Usa: '.implode(', ', array_keys(User::ROLES)));

            return self::FAILURE;
        }

        $email = $this->argument('email') ?? text('Email', required: true);
        $user = User::firstWhere('email', $email);

        if ($user) {
            $user->update(['role' => $rol, 'activo' => true]);
            $this->info("{$email} ahora es {$rol}.");

            return self::SUCCESS;
        }

        User::create([
            'name' => $this->option('nombre') ?? text('Nombre', required: true),
            'email' => $email,
            'password' => password('Contraseña (mínimo 8 caracteres)', required: true, validate: fn ($v) => strlen($v) < 8 ? 'Mínimo 8 caracteres' : null),
            'role' => $rol,
            'email_verified_at' => now(),
        ]);
        $this->info("Creado {$email} con rol {$rol}.");

        return self::SUCCESS;
    }
}
