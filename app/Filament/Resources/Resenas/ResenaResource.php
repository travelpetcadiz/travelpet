<?php

namespace App\Filament\Resources\Resenas;

use App\Filament\Resources\Resenas\Pages\EditResena;
use App\Filament\Resources\Resenas\Pages\ListResenas;
use App\Filament\Resources\Resenas\Schemas\ResenaForm;
use App\Filament\Resources\Resenas\Tables\ResenasTable;
use App\Models\Resena;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ResenaResource extends Resource
{
    protected static ?string $model = Resena::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Lo que envía la gente';

    protected static ?string $modelLabel = 'reseña';

    protected static ?string $pluralModelLabel = 'reseñas';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ResenaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResenasTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $n = Resena::where('estado', 'pendiente')->count();

        return $n ? (string) $n : null;
    }

    // Las reseñas las escriben los usuarios desde la web
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResenas::route('/'),
            'edit' => EditResena::route('/{record}/edit'),
        ];
    }
}
