<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')->label('Nombre')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                Select::make('role')
                    ->label('Rol')
                    ->options(User::ROLES)
                    ->required()
                    ->default('usuario')
                    ->helperText('Administrador: todo. Editor: sitios, reseñas y sugerencias. Usuario: solo la web pública.')
                    // Evita que un admin se quite el rol a sí mismo por error
                    ->disabled(fn (?User $record) => $record?->is(auth()->user())),
                Toggle::make('activo')
                    ->default(true)
                    ->helperText('Desactivado: no puede entrar ni publicar reseñas.')
                    ->disabled(fn (?User $record) => $record?->is(auth()->user())),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->dehydrateStateUsing(fn (string $state) => Hash::make($state))
                    ->helperText(fn (string $operation) => $operation === 'edit' ? 'Déjala vacía para no cambiarla.' : null),
            ]);
    }
}
