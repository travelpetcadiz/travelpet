<?php

use App\Models\Sitio;
use App\Support\DatosGuia;
use Illuminate\Database\Migrations\Migration;

/**
 * Rellena los campos nuevos de filtros y temporadas a partir de lo que ya estaba escrito.
 * Solo se marca lo que el texto dice claramente; lo dudoso queda en «no lo sabemos» (null)
 * para que Natalia lo complete en el panel. Cada cambio queda en el historial.
 */
return new class extends Migration
{
    public function up(): void
    {
        Sitio::$origenCambios = 'Clasificación automática (GitHub)';

        // Playas con fechas claras en su texto de temporada
        $playas = [
            ['Playa canina de Sancti Petri (Lavaculos)', 'Chiclana de la Frontera', 'todo', null, null],
            ['Zona canina de Torregorda', 'Cádiz', 'solo', '01/03', '31/10'],
            ['Playa de Los Bateles', 'Conil de la Frontera', 'prohibido', '01/05', '31/10'],
            ['Playa de La Fontanilla', 'Conil de la Frontera', 'prohibido', '01/05', '31/10'],
            ['Playa de Fuente del Gallo', 'Conil de la Frontera', 'prohibido', '01/05', '31/10'],
            ['Playa de Roche', 'Conil de la Frontera', 'prohibido', '01/05', '31/10'],
            ['Playa de Zahara de los Atunes', 'Barbate', 'prohibido', '01/06', '30/09'],
            ['Playa de Atlanterra', 'Tarifa', 'prohibido', '01/06', '30/09'],
            ['Playas de Sanlúcar', 'Sanlúcar de Barrameda', 'prohibido', '15/06', '15/09'],
            ['Playas de Chipiona', 'Chipiona', 'prohibido', '01/06', '30/09'],
            ['Playa de La Costilla', 'Rota', 'prohibido', '01/06', '30/09'],
            ['Playa de Punta Candor', 'Rota', 'prohibido', '01/06', '30/09'],
            ['Playas urbanas de Cádiz (normativa municipal)', 'Cádiz', 'prohibido', 'ramos', '30/09'],
            ['Playas de Chiclana (normativa municipal)', 'Chiclana de la Frontera', 'solo', '15/10', '15/03'],
        ];
        foreach ($playas as [$nombre, $municipio, $tipo, $desde, $hasta]) {
            $this->marcar($nombre, $municipio, ['periodo_tipo' => $tipo, 'periodo_desde' => $desde, 'periodo_hasta' => $hasta]);
        }

        // Veterinarios que indican «Urgencias 24 h»
        foreach ([
            ['Clínica Veterinaria Estadio (Antigua Bahía)', 'Cádiz'],
            ['Centro Veterinario Animalia', 'Chiclana de la Frontera'],
            ['Clínica Veterinaria Avila Chiclana', 'Chiclana de la Frontera'],
            ['Clínica Veterinaria Avila', 'El Puerto de Santa María'],
            ['Na Veterinarios', 'Jerez de la Frontera'],
            ['Zootécnica Jerezana', 'Jerez de la Frontera'],
        ] as [$nombre, $municipio]) {
            $this->marcar($nombre, $municipio, ['urgencias_24h' => true]);
        }

        // Bares y restaurantes: ¿pueden estar dentro o solo en la terraza? (según «zonas permitidas»)
        foreach ([
            'Cádiz' => ['Ultramar & Nos' => true, 'Saja River' => true, 'La Casapuerta' => true],
            'San Fernando' => ['La Tapería de Ari' => false],
            'Conil de la Frontera' => ['MALCAFÉ Coffee & Deli' => true, 'Restaurante Atalaya' => false],
            'Barbate' => ['Casa Juanito el Costero' => false, 'Chiringuito Pez Limón' => false, 'Tía Juana' => false],
            'Tarifa' => ['El Pasillo' => false, 'Surla Café' => false],
            'Jerez de la Frontera' => [
                'Why Not' => false, 'Krombol Bakery' => true, 'Cafetería Moka' => false, 'Cervecería Lola' => false,
                'La Embajada' => true, 'Tabankino Tabanco Gourmet' => true, 'Jindama' => true, 'Bina Bar' => true, 'Volapié' => true,
            ],
            'Sanlúcar de Barrameda' => ['Doña Calma Gastrobar' => false],
            'Chiclana de la Frontera' => ['Bodega El Carretero' => true, 'Le Club La Barrosa' => true],
            'Puerto Real' => ['Restaurante Raíces' => false],
        ] as $municipio => $locales) {
            foreach ($locales as $nombre => $dentro) {
                $this->marcar($nombre, $municipio, ['puede_entrar' => $dentro]);
            }
        }

        // Tamaño, suplemento y correa: reglas sobre el texto
        Sitio::query()->each(function (Sitio $s) {
            $cambios = array_filter([
                'admite_grandes' => $s->admite_grandes ?? $this->grandes($s->tamano_maximo),
                'sin_suplemento' => $s->sin_suplemento ?? $this->sinSuplemento($s->suplemento),
                'sin_correa' => $s->sin_correa ?? $this->sinCorrea($s),
            ], fn ($v) => $v !== null);
            if ($cambios) {
                $s->update($cambios);
            }
        });

        Sitio::$origenCambios = 'Sistema';
    }

    public function down(): void
    {
        // Los datos no se deshacen
    }

    private function marcar(string $nombre, string $municipio, array $campos): void
    {
        DatosGuia::buscar($nombre, $municipio)?->update($campos);
    }

    // ¿Admite perros de más de 25 kg?
    private function grandes(?string $t): ?bool
    {
        if (! $t || preg_match('/no (lo )?han indicado|no indica|l[ií]mite indicado|peligros/iu', $t)) {
            return null;
        }
        if (preg_match('/hasta (\d+)\s*kg|m[aá]ximo (\d+)\s*kg/iu', $t, $m)) {
            return (int) ($m[1] ?: $m[2]) > 25;
        }
        if (preg_match('/raza peque[ñn]a/iu', $t)) {
            return false;
        }
        if (preg_match('/sin (l[ií]mite|restricci[oó]n|limitaciones)|perros grandes|todos los tama/iu', $t)) {
            return true;
        }

        return null;
    }

    private function sinSuplemento(?string $t): ?bool
    {
        return match (true) {
            ! $t => null,
            (bool) preg_match('/^(gratis|sin suplemento)/iu', $t) => true,
            (bool) preg_match('/€|con suplemento|hay que a[ñn]adirla/iu', $t) => false,
            default => null,
        };
    }

    // Parques y playas: ¿hay zona donde pueden ir sueltos?
    private function sinCorrea(Sitio $s): ?bool
    {
        if (! in_array($s->tipo, ['Espacio', 'Playa'], true) || $s->admite_perros === 'no') {
            return null;
        }
        $t = implode(' ', array_filter([$s->zonas_permitidas, $s->otras_condiciones]));
        $canino = (bool) preg_match('/(parque|[aá]rea|zona|playa) canin|pipi/iu', $s->subtipo ?? '');

        if (preg_match('/suelt|sin correa|salvo en (la )?zona/iu', $t)) {
            return true;
        }
        // En parques y playas caninas se va suelto salvo que digan que la correa es obligatoria para todos
        // («perros nerviosos con correa» no cuenta)
        if ($canino) {
            return ! preg_match('/correa obligatoria|siempre (con correa|atado)|con correa y bozal/iu', $t);
        }

        return preg_match('/con correa|correa obligatoria|atado/iu', $t) ? false : null;
    }
};
