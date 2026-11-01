<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => User::ROLES[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'admin' => 'danger',
                        'editor' => 'warning',
                        default => 'gray',
                    }),
                IconColumn::make('activo')->boolean(),
                TextColumn::make('resenas_count')->label('Reseñas')->counts('resenas')->sortable(),
                TextColumn::make('created_at')->label('Registro')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->label('Rol')->options(User::ROLES),
                TernaryFilter::make('activo'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
