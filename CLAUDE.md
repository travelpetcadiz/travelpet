<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

# TravelPet

Guía de sitios que admiten perros. Empezó con la provincia de Cádiz (web estática original: github.com/travelpetcadiz/travelpet) y está pensada para crecer a más provincias.

## Diseño
- Antes de cualquier cambio visible (web o panel), usar la skill `diseno-travelpet` (`.claude/skills/diseno-travelpet/SKILL.md`): colores, componentes, iconos (Heroicons) y textos.

## Stack
- Laravel 13, Livewire 4 (componentes de clase en `app/Livewire`), Filament 5 (panel en `/admin`), Tailwind 4.
- Mapa: MapLibre GL. Usa `public/mapa/base.pmtiles` (Protomaps) si existe o `MAPA_PMTILES_URL`; si no, OpenFreeMap. El motor del mapa se carga bajo demanda (`resources/js/mapa-motor.js`).
- MySQL compartido del servidor (`mysql_container`, red `red_boty`) con usuario propio `travelpet`; tests en la base `travelpet_test`.

## Datos
- `provincias` → `municipios` → `sitios`. Una provincia con `activa = false` no sale en la web.
- Editar un sitio guarda cada campo cambiado en `cambios` (observer en `Sitio`). Las importaciones usan `saveQuietly()` para no ensuciar el historial.
- Reseñas y sugerencias entran como `pendiente` y se moderan en el panel.

## Cambiar datos desde el repositorio (migraciones de datos)
Natalia gestiona el día a día en el panel. Cuando pida cambios de datos a Claude (corregir sitios, añadir varios a la vez, ocultar uno), se hacen con una **migración** que usa `App\Support\DatosGuia`; al llegar a `main` se aplica sola en producción (`migrate --force` al desplegar).

1. **Consultar primero los datos actuales** en https://travelpetcadiz.es/datos/sitios.json (sitios publicados). Desde el repo no se ve la base de datos y Natalia edita en el panel, así que `database/data/datos.json` está desactualizado: no usarlo como referencia ni editarlo.
2. Crear la migración con `php artisan make:migration datos_<descripcion_corta>` y usar solo `DatosGuia` en `up()`; dejar `down()` vacío (los datos no se deshacen con rollback). Empezar `up()` con `if (! Sitio::exists()) return;`: los tests migran una base vacía y `crearOActualizar` falla si no existe la provincia, lo que pararía el despliegue.
3. Cambiar solo los campos pedidos. Nunca borrar sitios (usar `ocultar`), ni tocar `users`, `resenas` o `sugerencias`, ni hacer `truncate` o `update` masivos sin `where`.
4. `php artisan test` antes de subir.

```php
use App\Support\DatosGuia;

public function up(): void
{
    DatosGuia::actualizar('Bina Bar', 'Jerez de la Frontera', [
        'zonas_permitidas' => 'Dentro y en la terraza',
        'verificado' => true,
        'fecha_comprobacion' => '2026-11-01',
    ]);

    DatosGuia::crearOActualizar('Parque canino Lola Flores', 'Jerez de la Frontera', [
        'tipo' => 'Espacio', 'subtipo' => 'Parque canino', 'admite_perros' => 'si',
        'latitud' => 36.68, 'longitud' => -6.12, 'fuente' => 'Ayuntamiento de Jerez',
    ]);

    DatosGuia::ocultar('El Escondite de Inga', 'Chiclana de la Frontera');
}
```

- `tipo`: Alojamiento, Restauración, Playa, Espacio, Salud, Servicios, Transporte y ocio. `admite_perros`: si, condiciones, no, no_consta.
- Si el sitio no existe, `actualizar`/`ocultar` lo anotan en el log y siguen (no rompen el despliegue). Cada campo cambiado queda en el historial del panel con fuente «GitHub».
- Una migración solo se ejecuta una vez: si luego Natalia cambia ese sitio en el panel, su cambio se respeta.

## Roles (`users.role`)
- `admin`: todo, incluidos usuarios y provincias. `editor`: sitios, reseñas, sugerencias y municipios. `usuario`: solo la web.
- En closures de Filament, el parámetro del query builder se llama `$query` (Filament inyecta por nombre).

## Comandos
- `php artisan travelpet:importar [archivo] --provincia=Cádiz` importa un `datos.json` con el formato de la web estática.
- `php artisan travelpet:admin email@x.com [--rol=editor]` crea un admin/editor o cambia el rol de uno existente.
- `php artisan test` (29 tests). Tienen que pasar: si fallan, el despliegue no se publica.

## Despliegue
- `docker compose up -d --build` → contenedor `travelpet_app` (FrankenPHP) en `127.0.0.1:8070`. Migra e importa (si está vacío) al arrancar.
- El nginx del servidor hace de proxy: plantilla en `docker/nginx-travelpet.conf`.
- Despliegue automático: `docker/deploy.sh` (cron cada minuto) trae `origin/main`, pasa los tests, reconstruye y, si la web no responde, vuelve a la imagen `travelpet:anterior`. Clave de solo lectura en `~/.ssh/travelpet_deploy`. No editar en el servidor sin commitear: el despliegue se para si hay cambios locales.
