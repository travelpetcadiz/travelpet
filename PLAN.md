# Plan de TravelPet

Hola, Natalia. Este es el plan para que TravelPet pase de ser un buen listado a ser **la guía más clara y comprobada para viajar con mascota por Cádiz**.

No necesitas saber nada de programación. Para cada paso pone **qué haces tú** y **qué hace Claude**. Cuando abras Claude, basta con escribirle **«¿Qué toca ahora?»**: mirará este plan, te dirá el siguiente paso y te guiará.

---

## Antes de empezar: dónde está cada cosa

| Qué | Dónde | Para qué |
|---|---|---|
| **La web** | https://travelpetcadiz.es | Lo que ve todo el mundo |
| **El panel** | https://travelpetcadiz.es/admin | Donde cambias sitios, reseñas, sugerencias, usuarios y **fotos** |
| **Los bocetos** | https://travelpetcadiz.es/mockup | Donde verás el diseño nuevo antes de que llegue a la web. Solo lo ve el equipo |
| **Claude** | https://claude.ai/code, con el proyecto `travelpet` | Para pedir cambios de diseño o cambios de muchos sitios a la vez |

**Lo del día a día lo sigues haciendo en el panel**: un sitio nuevo, corregir un dato, aprobar una reseña…

**Cuando Claude termina un cambio, se publica solo** en uno o dos minutos. Si algo falla, la web se queda como estaba: no puedes romperla.

---

## Lo que ya tienes

- 525 sitios en 41 municipios, con sus condiciones y de dónde sale cada dato.
- La web dice **«mascota»** en lugar de «perro», porque es para cualquier animal de compañía.
- Iconos en todos los botones y una página **[Cómo funciona](https://travelpetcadiz.es/como-funciona)** para quien llega por primera vez.
- Un solo menú de categorías (antes salía repetido) con los mismos nombres en la web y en el panel: Dormir, Comer y beber, Playas, Parques y paseos…
- La web carga más rápido en el móvil: el mapa solo se descarga cuando alguien pulsa «Mapa».
- Una actualización de seguridad aplicada.
- La sección **Fotos** en el panel, para subir tus fotos reales.
- **«Cerca de mí»**: ordena por distancia.
- **«¿Se puede ir hoy?»** en las playas: la ficha dice «Hoy se puede ir» o «Hoy no» según las fechas de cada playa (incluido el Domingo de Ramos de Cádiz, que cambia cada año).
- **Urgencias veterinarias 24 h** desde la portada (o en https://travelpetcadiz.es/urgencias), con botón para llamar directamente.
- **Filtros prácticos**: perros grandes, sin suplemento, dentro del local, zona sin correa y playas abiertas hoy.
- **Compartir** cada sitio y tu «Mi viaje» por WhatsApp.
- Google ya sabe, en cada ficha, qué tipo de sitio es y si admite mascotas.
- **Fotos en cada ficha**, con su crédito debajo. Ninguna sale hasta que la publicas tú. Al subirlas se les quita la ubicación GPS y se hacen ligeras.
- **Fotos libres de Wikimedia**: ya hay fotos candidatas de playas y parques esperando tu revisión (en el panel: Inicio › «Fotos por revisar»).

---

## El plan, paso a paso

### Paso 1. Sube tus fotos ✋ *(lo haces tú)*

El diseño nuevo necesita **fotos reales tuyas**, no de internet. Son lo que hará que TravelPet se sienta de verdad, de Cádiz.

**Cómo subirlas** (unos 2 minutos por foto):
1. Entra en el panel: https://travelpetcadiz.es/admin
2. En el menú de la izquierda, pulsa **Fotos** y luego **Nueva foto**.
3. Arrastra la foto o pulsa para elegirla desde el móvil o el ordenador.
4. En **«Qué se ve»** escribe una frase sencilla, por ejemplo: *«Luna tumbada en la arena de la playa de Bateles»*.
5. Pon dónde está hecha y marca **«Me gustaría que saliera en la portada»** en tus favoritas.
6. Pulsa **Crear**.

**Qué fotos van mejor** (de 5 a 10 es perfecto):
- Tu mascota (u otras, con permiso de sus dueños) **en la costa o los pueblos de Cádiz**: playa, terraza, paseo, casa rural…
- **En horizontal**, con buena luz de día y que se vea dónde estáis.
- Mejor sin caras de desconocidos.
- Hechas por ti, o con permiso de quien las hizo.

**Y las fotos de cada sitio** (salen en su ficha):
- **Playas y parques:** en el panel, Inicio › **Fotos por revisar**. Son fotos libres de Wikimedia. Mira que sean de verdad ese sitio y que no salga nadie reconocible, y pulsa **Publicar** en la que más te guste.
- **Hoteles, bares y negocios:** pídeles fotos con el correo modelo que tienes en el panel, en **Ayuda › Correo para pedir fotos a un negocio** (tiene un botón para copiarlo). Cuando te las manden, súbelas en la ficha del sitio, pestaña **Fotos de este sitio**, eligiendo «Me la ha cedido el propio sitio».
- **Nunca** copies fotos de Google, Booking o Instagram: tienen dueño.

👉 Cuando las tengas, dile a Claude: **«Ya he subido las fotos»**.

### Paso 2. Los bocetos del diseño nuevo 🤖 *(lo hace Claude)*

Claude prepara tres pantallas, para el móvil y para el ordenador:
- **La portada:** una foto tuya, el buscador grande «¿Dónde quieres ir con tu mascota?», el botón «Usar mi ubicación», los destinos (Cádiz, Conil, Tarifa, Chiclana, El Puerto…) y por qué fiarse de TravelPet.
- **Los resultados**, en lista y en mapa, con tarjetas más claras.
- **La ficha de un sitio**, con la respuesta (sí, con condiciones o no) bien grande, las condiciones con iconos y los botones de llegar, llamar y guardar siempre a mano en el móvil.

Los verás en https://travelpetcadiz.es/mockup. **La web real no cambia todavía.**

👉 Dile a Claude: **«Prepárame los bocetos»**.

### Paso 3. Revisa los bocetos y di qué cambiar ✋ *(lo haces tú)*

Míralos con calma, sobre todo **en el móvil**, y díselo a Claude con tus palabras. Por ejemplo:
- *«La foto de la portada mejor la de Bolonia.»*
- *«El botón de llamar no se ve, hazlo más grande.»*
- *«Me gusta, pero quita lo de los municipios.»*

Claude lo cambia y lo vuelves a mirar, tantas veces como haga falta.

👉 Cuando te guste todo, dile: **«Aprobado, ponlo en la web»**.

### Paso 4. El diseño nuevo, en la web 🤖 *(lo hace Claude)*

Claude pasa a la web real lo que hayas aprobado, sin tocar tus datos. Después te pide que eches un vistazo final.

### Paso 5. «Cerca de mí» ✅ *(hecho)*

Ya está en la web: el botón «Cerca de mí» ordena los sitios por distancia y muestra «a 800 m», «a 3 km»… La ubicación solo se usa en el móvil de quien lo pulsa: no se guarda.

### Paso 6. Páginas para que te encuentren en Google 🤖 + ✋

Páginas propias como *«Playas para mascotas en Cádiz»*, *«Restaurantes que admiten mascotas en Conil»* o *«Alojamientos en Tarifa»*. Es lo que más visitas traerá.
- **Claude** las crea a partir de tus datos.
- **Tú** puedes escribir un texto corto de introducción para cada una, con lo que sabes de cada pueblo.

### Paso 7. Que la gente participe 🤖 + ✋

Todavía no hay reseñas. Claude hará más visibles las invitaciones a guardar sitios, opinar y avisar de cambios. **Tú** puedes ayudar a arrancar pidiendo a conocidos con mascota que dejen la primera reseña de los sitios a los que han ido.

### Paso 8. Revisar los datos poco a poco ✋ + 🤖

Casi todo se comprobó en octubre de 2026. **Hacia abril de 2027 caducarán cientos de sitios a la vez.** Claude añadirá al panel una lista de «sitios que caducan este mes», para que los revises poco a poco y no todos de golpe.

### Paso 9. Saber qué funciona 🤖 *(lo hace Claude)*

Contar cuánta gente busca, llama, pide la ruta o guarda sitios, **sin cookies de publicidad ni avisos molestos**. Lo verás en el resumen del panel.

### Paso 10. Más adelante: los negocios 🤖 + ✋

Que un hotel o un bar pueda reclamar su ficha y confirmar sus datos. Tú decides a quién se le da acceso.

---

## Dos cosas que puedes completar en el panel ✋

1. **Fechas de las playas.** Ya tienen fechas 14 playas. A las que solo dicen «temporada de verano» (Los Manolos, Santa Clara, Bonanza, El Aculadero…) les falta saber del día tal al día cual. Cuando lo sepas: Panel › Sitios › la playa › «Fechas en que se puede ir».
2. **Datos para los filtros.** Se rellenaron solos a partir de lo que ya estaba escrito, solo cuando el texto lo decía claro. Lo que no se sabe pone «No lo sabemos» y no sale en los filtros. Cuando confirmes algo con un sitio (si admite perros grandes, si cobra suplemento, si se puede estar dentro…), márcalo en «Para los filtros». En el Historial verás lo que se rellenó solo como «Clasificación automática».

## Datos que faltan en el aviso legal ✋ (importante)

La ley obliga a que la web diga quién es su titular. Ahora el aviso legal y la privacidad muestran «[PENDIENTE]». Pásale a tu desarrollador (no por aquí): **nombre y apellidos (o el nombre de la empresa), NIF y una dirección postal**. Él los pone en el servidor y salen solos en las páginas legales.

Las páginas legales ya están hechas: [aviso legal](https://travelpetcadiz.es/aviso-legal), [privacidad](https://travelpetcadiz.es/privacidad), [cookies](https://travelpetcadiz.es/cookies) y [condiciones de uso](https://travelpetcadiz.es/condiciones).

## Una decisión pendiente para ti ✋

La web ya habla de **mascotas**, pero **casi todos los datos se preguntaron por perros**. Un hotel que admite perros no siempre admite gatos.

**Propuesta:** añadir a cada sitio un dato «**Qué animales admite**» (perros, gatos, otros) y, mientras no se sepa, mostrar «Confirmado para perros». Díselo a Claude si te parece bien: **«Añade qué animales admite cada sitio»**.

---

## Cómo hablar con Claude

Háblale como a una persona. Algunos ejemplos:

- **«¿Qué toca ahora?»**: te dice el siguiente paso del plan.
- **«Explícame qué es esto»**: te lo explica sin palabras técnicas.
- **«Cambia el color del botón de llamar»**, **«pon más grande el mapa en el móvil»**: cambios de diseño.
- **«Añade estos 6 parques de Jerez»**, **«oculta El Escondite de Inga, que ha cerrado»**: cambios de muchos sitios a la vez. Para un solo sitio es más rápido el panel.
- **«¿Ya está publicado?»**: te dice si el cambio ya se ve en la web.

Si Claude te pide algo que no entiendes o que no sabes hacer (el servidor, contraseñas, cosas técnicas), dile **«eso no sé hacerlo»** y te dirá que se lo pases a tu desarrollador.

---

## Seguimiento

Claude marca aquí lo que se va terminando.

- [x] Iconos, página «Cómo funciona», «mascota» en toda la web y un solo menú de categorías
- [x] Seguridad y mapa más rápido
- [x] Sección «Fotos» en el panel y página de bocetos
- [x] Paso 1. Subir fotos
- [x] Paso 2. Bocetos
- [ ] Paso 3. Revisión de bocetos
- [ ] Paso 4. Diseño nuevo en la web
- [x] Paso 5. «Cerca de mí»
- [x] «¿Se puede ir hoy?» en playas, urgencias 24 h, filtros prácticos, compartir y datos para Google
- [x] Paso 6. Páginas para Google
- [ ] Paso 7. Participación
- [ ] Paso 8. Revisión escalonada de datos
- [ ] Paso 9. Medir el uso
- [ ] Paso 10. Negocios
- [ ] Decisión: qué animales admite cada sitio
