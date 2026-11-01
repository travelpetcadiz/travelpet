<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sitio_id', 'user_id', 'sitio_nombre', 'municipio', 'campo', 'antes', 'despues', 'fuente', 'fecha'])]
class Cambio extends Model
{
    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function sitio(): BelongsTo
    {
        return $this->belongsTo(Sitio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
