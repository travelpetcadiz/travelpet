<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.3. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Despliegue

- TravelPet no usa Laravel Cloud ni ningún hosting externo: está en un servidor propio con Docker (ver «Despliegue» en las notas de TravelPet de este archivo).
- Para publicar un cambio basta con subirlo a la rama `main`: el servidor lo descarga, pasa los tests y lo publica solo en uno o dos minutos. No hace falta ni se debe tocar el servidor.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>

# TravelPet

Guía de sitios que admiten mascotas. Empezó con la provincia de Cádiz (web estática original: github.com/travelpetcadiz/travelpet) y está pensada para crecer a más provincias.

## Si quien escribe es Natalia (dueña de TravelPet)
Natalia no es informática. Con ella:
- **Al empezar, lee `PLAN.md`** y, si pregunta «¿qué toca ahora?» o no sabe por dónde seguir, dile en dos o tres frases cuál es el siguiente paso sin marcar, qué tiene que hacer ella (✋) y qué harás tú (🤖). Un paso cada vez.
- Habla claro y en español, sin jerga (nada de «commit», «push», «migración», «deploy»; di «guardar y publicar el cambio», «se publica solo en un par de minutos»). Si algo técnico es inevitable, explícalo con un ejemplo.
- Nunca le pidas que use la terminal, git ni el servidor. Lo que necesite el servidor o contraseñas: dile que se lo pase a su desarrollador.
- Ella lo ve en la web: cuando termines, dile qué dirección abrir para verlo (la web, `/admin` o `/mockup`) y avísale de que tarda uno o dos minutos en publicarse.
- Al terminar un paso, márcalo con `[x]` en «Seguimiento» de `PLAN.md` en el mismo cambio.
- Los bocetos van en `resources/views/mockup/<nombre>.blade.php` (se ven en `/mockup/<nombre>`, solo el equipo) y usan las fotos que sube en el panel (`App\Models\Foto`, `para_portada`). No toques las pantallas reales hasta que diga que lo aprueba.

## Trabajar siempre sobre lo último
Varias personas (y varios asistentes) cambian este repositorio: Natalia con Claude, su desarrollador y otros agentes.
- **Antes de empezar** y **justo antes de subir**: `git pull --rebase origin main`. Si hay cambios nuevos, léelos (`git log -p`) antes de seguir para no deshacer el trabajo de otro.
- Sube cambios pequeños y frecuentes a `main` en lugar de uno enorme al final.
- En el servidor no se edita nada sin subirlo: el despliegue automático se para si encuentra cambios a medias.
- Si alguna vez hay que reconstruir a mano en el servidor, con el mismo candado que el despliegue automático: `flock /tmp/travelpet-deploy.lock docker compose up -d --build`.

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
- Campos para filtrar (`admite_grandes`, `sin_suplemento`, `puede_entrar`, `sin_correa`): `null` = no lo sabemos y no sale en el filtro. `urgencias_24h` para veterinarios. Playas: `periodo_tipo` (todo | solo | prohibido) con `periodo_desde`/`periodo_hasta` en DD/MM o `ramos`; `Sitio::hoy()` dice si hoy se puede ir.

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

## Páginas legales (tienen que decir la verdad)
- `/aviso-legal`, `/privacidad`, `/cookies` y `/condiciones` (`resources/views/legal/`). Los datos del titular salen de `TITULAR_*` en el `.env` del servidor.
- **Si añades algo que trate datos o guarde cosas en el navegador** (analítica, una cookie, `localStorage`, un formulario, un proveedor nuevo, correo…), actualiza en el mismo cambio la privacidad o las cookies y su fecha de «Última actualización» (`x-pagina-legal`). Hoy no hay analítica ni cookies de terceros, y por eso no hay banner de cookies: si se añadieran, haría falta pedir consentimiento.
- Plazos prometidos: contacto de negocios 12 meses tras revisarlo (`travelpet:limpiar-datos`, cada noche), registros del servidor 14 días, copias unos días.
- Registro: 14 años o más (art. 7 LOPDGDD).

## Roles (`users.role`)
- `admin`: todo, incluidos usuarios y provincias. `editor`: sitios, reseñas, sugerencias y municipios. `usuario`: solo la web.
- En closures de Filament, el parámetro del query builder se llama `$query` (Filament inyecta por nombre).

## Comandos
- `php artisan travelpet:importar [archivo] --provincia=Cádiz` importa un `datos.json` con el formato de la web estática.
- `php artisan travelpet:admin email@x.com [--rol=editor]` crea un admin/editor o cambia el rol de uno existente.
- `php artisan test`. Tienen que pasar: si fallan, el despliegue no se publica.

## Despliegue
- `docker compose up -d --build` → contenedor `travelpet_app` (FrankenPHP) en `127.0.0.1:8070`. Migra e importa (si está vacío) al arrancar.
- El nginx del servidor hace de proxy: plantilla en `docker/nginx-travelpet.conf`.
- Despliegue automático: `docker/deploy.sh` (cron cada minuto) trae `origin/main`, pasa los tests, reconstruye y, si la web no responde, vuelve a la imagen `travelpet:anterior`. Clave de solo lectura en `~/.ssh/travelpet_deploy`. No editar en el servidor sin commitear: el despliegue se para si hay cambios locales.
