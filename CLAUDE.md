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

## Stack
- Laravel 13, Livewire 4 (componentes de clase en `app/Livewire`), Filament 5 (panel en `/admin`), Tailwind 4.
- Mapa: MapLibre GL. Usa `public/mapa/base.pmtiles` (Protomaps) si existe o `MAPA_PMTILES_URL`; si no, OpenFreeMap. El motor del mapa se carga bajo demanda (`resources/js/mapa-motor.js`).
- MySQL compartido del servidor (`mysql_container`, red `red_boty`) con usuario propio `travelpet`; tests en la base `travelpet_test`.

## Datos
- `provincias` → `municipios` → `sitios`. Una provincia con `activa = false` no sale en la web.
- Editar un sitio guarda cada campo cambiado en `cambios` (observer en `Sitio`). Las importaciones usan `saveQuietly()` para no ensuciar el historial.
- Reseñas y sugerencias entran como `pendiente` y se moderan en el panel.

## Roles (`users.role`)
- `admin`: todo, incluidos usuarios y provincias. `editor`: sitios, reseñas, sugerencias y municipios. `usuario`: solo la web.
- En closures de Filament, el parámetro del query builder se llama `$query` (Filament inyecta por nombre).

## Comandos
- `php artisan travelpet:importar [archivo] --provincia=Cádiz` importa un `datos.json` con el formato de la web estática.
- `php artisan travelpet:admin email@x.com [--rol=editor]` crea un admin/editor o cambia el rol de uno existente.
- `php artisan test` (24 tests).

## Despliegue
- `docker compose up -d --build` → contenedor `travelpet_app` (FrankenPHP) en `127.0.0.1:8070`. Migra e importa (si está vacío) al arrancar.
- El nginx del servidor hace de proxy: plantilla en `docker/nginx-travelpet.conf`.
- Despliegue automático: `docker/deploy.sh` (cron cada minuto) trae `origin/main`, pasa los tests, reconstruye y, si la web no responde, vuelve a la imagen `travelpet:anterior`. Clave de solo lectura en `~/.ssh/travelpet_deploy`. No editar en el servidor sin commitear: el despliegue se para si hay cambios locales.
