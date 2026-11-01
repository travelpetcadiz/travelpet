---
name: diseno-travelpet
description: Reglas de diseño y estilo de TravelPet (colores, fuentes, botones, iconos, textos y comprobaciones). Úsala SIEMPRE antes de cambiar cualquier cosa visible de la web pública o del panel: páginas, componentes Blade/Livewire, CSS, iconos, textos de botones o mensajes. También cuando pidan que algo sea "más bonito", "más claro", "más intuitivo" o "más fácil".
---

# Diseño de TravelPet

TravelPet responde a una pregunta: **«¿Puedo ir con mi mascota?»**. La usa gente con prisa, casi siempre desde el móvil y sin conocimientos técnicos. Todo cambio visual tiene que hacer esa respuesta más rápida y más clara. Si un adorno no ayuda a eso, no va.

## Principios

1. **La respuesta primero.** En tarjetas y fichas, lo primero es si admite mascotas (`<x-admite>`) y si está confirmado (`<x-confirmado>`). Luego, las condiciones. Lo demás, después.
2. **Icono + texto, siempre juntos.** Ningún botón ni enlace del menú lleva solo icono, ni solo texto. Los iconos ayudan a reconocer; el texto explica.
3. **Móvil primero.** Diseña para 390 px de ancho y luego amplía. En el móvil, lo secundario va plegado (como «Filtros»).
4. **Explica lo que no es obvio en el sitio donde surge la duda**: una línea de ayuda junto al elemento, no un manual aparte. Las dudas generales van en `/como-funciona` (`resources/views/paginas/como-funciona.blade.php`): si añades una función que la gente pueda no entender, añádela también ahí.
5. **Lo que no sabemos se dice** («Aún no sabemos…», «Sin confirmar»), en lugar de esconderlo o rellenar con «No consta».
6. **Veterinarios y servicios para mascotas** (`Sitio::SERVICIOS`) no llevan la etiqueta de admite mascotas; muestran el horario (campo `temporada`). Usa `$sitio->esServicio()`.

## Colores (tokens de `resources/css/app.css`)

Usa solo estos tokens con las clases de Tailwind (`bg-pino`, `text-gris`, `border-linea`…). No añadas colores sueltos en hexadecimal.

| Token | Uso |
|---|---|
| `pino` #0E7F8A / `pino-oscuro` / `pino-suave` | Marca, acciones principales (`btn-pri`), enlaces, fondo de la portada |
| `albero` #EBAA3C / `albero-suave` | Acción destacada de la ficha («Cómo llegar», `btn-albero`) y estrellas |
| `ok` / `ok-suave` | Admite mascotas, confirmado |
| `aviso` / `aviso-suave` | Con condiciones, datos antiguos |
| `mal` / `mal-suave` | No admite, errores |
| `tinta` #132A2E, `gris` #56696D | Texto principal y secundario |
| `cal` #F6F9F9, `linea` #DCE5E6 | Fondo de página y bordes |

Los colores de cada categoría están en `Sitio::TIPOS` (junto a su etiqueta e icono). Se muestran con `<x-icono-tipo :tipo="$sitio->tipo" />`.

## Tipografía

- **Bricolage Grotesque** (`font-display`): solo títulos `h1`–`h3` y la respuesta grande de la ficha.
- **Atkinson Hyperlegible** (`font-sans`, por defecto): todo lo demás. Está pensada para leerse bien con baja visión: no la cambies.
- Las fuentes se sirven desde el propio servidor (`@fontsource`); no cargues Google Fonts.
- Mayúscula solo al principio de la frase. Nada en MAYÚSCULAS. No pongas una palabra del titular en otro color o en cursiva para «destacarla».

## Componentes (en `resources/css/app.css` y `resources/views/components`)

| Clase / componente | Para qué |
|---|---|
| `.btn`, `.btn btn-pri`, `.btn btn-albero` | Botones. `btn-pri` = la acción principal de la pantalla (una sola). |
| `.chip` + `aria-pressed` | Filtros de categoría que se activan y desactivan |
| `.campo`, `.etiqueta`, `.error` | Campos de formulario, su etiqueta y el mensaje de error |
| `.tarjeta` | Caja blanca con borde: elementos de lista y bloques de la ficha |
| `.sello` | Etiqueta pequeña con icono (ya lleva `gap`) |
| `<x-admite :valor="...">` | Respuesta: si, condiciones, no, no_consta |
| `<x-confirmado />` | El propio sitio lo ha confirmado (`verificado`) |
| `<x-icono-tipo :tipo="...">` | Icono de la categoría con su color |

Antes de crear un estilo nuevo, comprueba si alguno de estos ya sirve.

## Iconos

- **Heroicons** (vienen con `blade-ui-kit/blade-heroicons`, los mismos que usa el panel Filament). Catálogo: https://heroicons.com
- En botones y menú: contorno, `@svg('heroicon-o-map', 'size-5', ['aria-hidden' => 'true'])`.
- En etiquetas pequeñas (`.sello`): relleno, `heroicon-s-...` con `size-4`.
- Siempre `aria-hidden="true"`: el texto de al lado ya dice lo que hace.
- Significados fijos (no los cambies de sentido): `magnifying-glass` buscar · `map` mapa/cómo llegar · `list-bullet` lista · `bookmark` Mi viaje/guardar · `heart` quiero ir (y veterinarios como categoría) · `check` he ido · `phone` llamar/contacto · `flag` avisar de un error · `light-bulb` sugerir · `chat-bubble-left-right` reseñas · `adjustments-horizontal` filtros · `question-mark-circle` ayuda · `pencil-square` editar · `user-plus` crear cuenta · `arrow-right-end-on-rectangle` entrar · `arrow-left-start-on-rectangle` salir · `share` compartir · `map-pin` ubicación y «Cerca de mí» · `calendar-days` fechas y «¿Se puede ir hoy?» · `lifebuoy` urgencias veterinarias · `scale` perros grandes · `banknotes` suplemento · `home` dentro del local · `bolt` sin correa.
- No uses emojis ni caracteres (✓ ♡ ▾ →) como iconos.

## Datos para filtrar

Los filtros prácticos (perros grandes, sin suplemento, dentro del local, sin correa, urgencias 24 h, «¿Se puede ir hoy?») salen de campos propios del sitio, no del texto. `null` significa «no lo sabemos»: no se muestra ni entra en el filtro. Nunca rellenes uno de estos campos suponiendo; si el texto no lo dice claro, déjalo en `null`.

## Textos

- **Hablamos de «mascota», no de «perro».** La guía es para cualquier animal de compañía: «¿Puedo ir con mi mascota?», «Admite mascotas», «Servicios para mascotas». Usa «perro» solo cuando el dato es concreto de perros (por ejemplo, «Solo perros de menos de 10 kg» en las condiciones de un sitio) o para describir una foto en la que sale un perro. Los nombres internos (`admite_perros`, `tamano_perro`) no se ven y no se cambian.
- Español de España, tú, frases cortas y verbos de acción: «Guardar», «Cómo llegar», «Enviar reseña». Nunca «Enviar» o «Aceptar» a secas si se puede decir qué hace.
- La misma acción se llama igual en todas partes (si el botón dice «Guardar», el estado es «Guardado»).
- Nombra las cosas como las entiende la gente, no como se llaman en la base de datos: «Confirmado», no «verificado»; «Dormir», no «Alojamiento» en los accesos de la portada.
- Los mensajes de error dicen qué ha pasado y cómo arreglarlo, sin disculpas. Una pantalla vacía propone qué hacer («Quitar filtros», «Sugerir un sitio»).
- Sin flechas «→» al final de enlaces o botones.

## Panel de administración (Filament)

Usa los componentes e iconos de Filament (`Heroicon::Outlined...`), etiquetas en español y `helperText` para explicar cualquier campo que pueda generar dudas. Color principal: `#0E7F8A`.

## Antes de dar el cambio por terminado

1. Míralo a 390 px y a 1440 px de ancho: nada cortado ni desbordado, botones de al menos 44 px de alto.
2. Navega con el teclado (Tab): se ve el foco en cada botón y enlace.
3. Cada icono nuevo tiene texto al lado y `aria-hidden`.
4. `npm run build` sin errores y `php artisan test` en verde: si fallan, el cambio no se publica.
