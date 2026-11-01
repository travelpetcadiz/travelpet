<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['archivo', 'descripcion', 'lugar', 'autor', 'para_portada', 'user_id'])]
class Foto extends Model
{
    protected $attributes = [
        'para_portada' => false,
    ];

    protected function casts(): array
    {
        return ['para_portada' => 'boolean'];
    }

    protected static function booted(): void
    {
        // Al borrar la ficha de la foto, se borra también el archivo
        static::deleted(fn (Foto $foto) => Storage::disk('public')->delete($foto->archivo));
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->archivo);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
