# TravelPet

Guía gratuita de sitios que admiten mascotas. Web: https://travelpetcadiz.es

La versión estática anterior (index.html + datos.json en Netlify) está guardada en la etiqueta y la rama `web-estatica`.

## Cómo se trabaja ahora

- **Los sitios, reseñas, sugerencias y usuarios se gestionan en el panel**: https://travelpetcadiz.es/admin. Viven en la base de datos, no en este repositorio. `database/data/datos.json` solo se usó para la carga inicial: editarlo no cambia la web.
- **Este repositorio es el código y el diseño.** Cada cambio que llega a `main` se publica solo en el servidor en unos minutos, si pasan los tests. Si fallan, la web se queda como estaba.

## Stack

Laravel 13 · Livewire 4 · Filament 5 (panel) · Tailwind 4 · MapLibre (mapa) · MySQL · Docker (FrankenPHP).

## Desarrollo

```sh
composer install && npm install
cp .env.example .env && php artisan key:generate   # configurar DB_* en .env
php artisan migrate && php artisan travelpet:importar
php artisan travelpet:admin tu@email.com
composer run dev
php artisan test
```

Más detalles para quien trabaje con Claude Code: `CLAUDE.md`.
