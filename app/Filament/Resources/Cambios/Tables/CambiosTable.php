<?php

namespace App\Filament\Resources\Cambios\Tables;

use App\Filament\Resources\Sitios\SitioResource;
use App\Models\Cambio;
use App\Models\Sitio;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CambiosTable
{
    // Valores guardados con su nombre de la web («si» → «Sí», «Alojamiento» → «Dormir»)
    private static function valor(?string $valor, Cambio $cambio): ?string
    {
        return match ($cambio->campo) {
            'admite_perros' => Sitio::ADMITE[$valor] ?? $valor,
            'tipo' => Sitio::TIPOS[$valor][0] ?? $valor,
            'periodo_tipo' => Sitio::PERIODOS[$valor] ?? $valor,
            default => $valor,
        };
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('sitio'))
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->searchPlaceholder('Buscar por sitio')
            ->emptyStateHeading('Aún no hay cambios')
            ->emptyStateDescription('Cuando cambies algo de un sitio, quedará apuntado aquí.')
            ->emptyStateIcon('heroicon-o-clock')
            ->columns([
                TextColumn::make('fecha')->label('Cuándo')->date('d/m/Y')->sortable(),
                TextColumn::make('sitio_nombre')->label('Sitio')->searchable()->weight('bold')->wrap()
                    ->description(fn (Cambio $record) => $record->municipio),
                TextColumn::make('campo')->label('Qué se cambió')
                    ->formatStateUsing(fn (string $state) => Sitio::CAMPOS[$state] ?? $state),
                TextColumn::make('antes')->label('Antes')->limit(60)->wrap()->placeholder('(vacío)')
                    ->formatStateUsing(fn (?string $state, Cambio $record) => self::valor($state, $record)),
                TextColumn::make('despues')->label('Ahora')->limit(60)->wrap()->placeholder('(vacío)')
                    ->formatStateUsing(fn (?string $state, Cambio $record) => self::valor($state, $record)),
                TextColumn::make('fuente')->label('Quién')->limit(40)->wrap(),
            ])
            ->filters([
                SelectFilter::make('campo')
                    ->label('Qué se cambió')
                    ->options(fn () => Cambio::query()->distinct()->orderBy('campo')->pluck('campo')
                        ->mapWithKeys(fn ($c) => [$c => Sitio::CAMPOS[$c] ?? $c])->all()),
                SelectFilter::make('fuente')
                    ->label('Quién')
                    ->options(fn () => Cambio::query()->distinct()->orderBy('fuente')->whereNotNull('fuente')->pluck('fuente', 'fuente')->all()),
            ])
            ->recordActions([
                Action::make('sitio')
                    ->label('Abrir el sitio')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn (Cambio $record) => $record->sitio !== null)
                    ->url(fn (Cambio $record) => SitioResource::getUrl('edit', ['record' => $record->sitio])),
            ]);
    }
}
