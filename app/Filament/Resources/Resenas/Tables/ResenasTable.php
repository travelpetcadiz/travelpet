<?php

namespace App\Filament\Resources\Resenas\Tables;

use App\Models\Resena;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ResenasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('sitio.nombre')->label('Sitio')->searchable()->wrap(),
                TextColumn::make('user.name')->label('Autor')->searchable(),
                TextColumn::make('estrellas')->formatStateUsing(fn (int $state) => str_repeat('★', $state).str_repeat('☆', 5 - $state)),
                TextColumn::make('pudo_entrar')
                    ->label('Entró')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'si' ? 'Sí' : 'No')
                    ->color(fn (string $state) => $state === 'si' ? 'success' : 'danger'),
                TextColumn::make('texto')->limit(80)->wrap(),
                TextColumn::make('estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Resena::ESTADOS[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'aprobada' => 'success',
                        'rechazada' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')->options(Resena::ESTADOS)->default('pendiente'),
            ])
            ->recordActions([
                Action::make('aprobar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Resena $record) => $record->estado !== 'aprobada')
                    ->action(fn (Resena $record) => $record->update(['estado' => 'aprobada'])),
                Action::make('rechazar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (Resena $record) => $record->estado !== 'rechazada')
                    ->action(fn (Resena $record) => $record->update(['estado' => 'rechazada'])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('aprobar')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['estado' => 'aprobada'])),
                    BulkAction::make('rechazar')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn (Collection $records) => $records->each->update(['estado' => 'rechazada'])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
