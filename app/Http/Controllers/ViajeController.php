<?php

namespace App\Http\Controllers;

use App\Models\Sitio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViajeController extends Controller
{
    /**
     * Guarda o quita sitios de Mi viaje de la cuenta. Recibe { items: { id: "quiero" | "visitado" | null } }:
     * un sitio con null se quita. También sirve para pasar a la cuenta lo guardado antes de entrar.
     */
    public function guardar(Request $request): JsonResponse
    {
        $items = $request->validate([
            'items' => ['present', 'array', 'max:200'],
            'items.*' => ['nullable', 'in:quiero,visitado'],
        ])['items'];

        $user = $request->user();
        $ids = collect(array_keys($items))->filter(fn ($id) => ctype_digit((string) $id))->map(fn ($id) => (int) $id);
        $validos = Sitio::publicados()->whereKey($ids)->pluck('id');

        foreach ($validos as $id) {
            $items[$id] === null
                ? $user->viaje()->detach($id)
                : $user->viaje()->syncWithoutDetaching([$id => ['estado' => $items[$id]]]);
        }

        return response()->json(['items' => (object) $user->viajeItems()]);
    }
}
