# Guía de Exposición — Roig Arena

---

## 1. Qué es el proyecto

**Roig Arena** es una aplicación web completa de venta de entradas para eventos en un estadio. Permite a los usuarios registrarse, ver eventos, seleccionar asientos concretos, reservarlos temporalmente (15 minutos) y confirmar la compra obteniendo una entrada con código QR.

Tiene dos tipos de usuario:
- **Usuario normal**: navega eventos, reserva y compra entradas.
- **Administrador**: gestiona eventos y sectores del estadio.

---

## 2. Tecnologías utilizadas y por qué

| Tecnología | Para qué se usa |
|---|---|
| **Laravel 12** | Framework PHP principal. Proporciona routing, ORM, validaciones, middleware y mucho más. |
| **PHP 8.3** | Lenguaje del backend. |
| **MySQL 8.4** | Base de datos relacional donde se guardan todos los datos. |
| **Laravel Sanctum** | Gestiona la autenticación. Genera tokens Bearer para la API y protege rutas de sesión web. |
| **Laravel Sail** | Entorno de desarrollo basado en Docker. Levanta la app, MySQL y phpMyAdmin con un comando. |
| **Docker** | Contenedores que garantizan que el entorno funciona igual en cualquier máquina. |
| **AWS EC2** | Servidor en la nube donde está desplegado el proyecto en producción. |
| **Blade** | Motor de plantillas de Laravel para generar el HTML del frontend. |
| **CSS + JS vanilla** | Sin frameworks frontend (sin React, Vue ni Alpine). Todo hecho a mano. |
| **PHPUnit** | Framework de testing. Se usa para los 49 tests automatizados. |

---

## 3. Arquitectura del proyecto

El proyecto separa claramente **API REST** y **Frontend Web**:

```
Navegador
    │
    ├── Rutas Web (routes/web.php)
    │       └── Controladores Web (app/Http/Controllers/Web/)
    │               └── Llaman directamente a los controladores API en memoria
    │                       └── No hacen llamadas HTTP (evita deadlock con PHP-FPM)
    │
    └── Rutas API (routes/api.php)
            └── Controladores API (app/Http/Controllers/)
                    └── Devuelven JSON
                            └── Usan Servicios (app/Services/)
                                    └── Acceden a Modelos Eloquent
                                            └── MySQL
```

**Por qué los controladores Web no hacen llamadas HTTP:**
Con un solo worker PHP-FPM (como en Laravel Sail), si el controlador Web hace una petición HTTP a la misma app, el worker queda bloqueado esperando una respuesta que nunca llega porque no hay otro worker libre → timeout de 30 segundos. La solución es llamar al controlador API directamente en memoria: `app(EventoController::class)->index()`.

---

## 4. Base de datos

### Tablas y su propósito

| Tabla | Contenido |
|---|---|
| `users` | Usuarios registrados. Tiene campo `is_admin` para distinguir administradores. |
| `sectores` | Zonas del estadio (Pista, Tribuna Norte, etc.). 71 sectores. |
| `asientos` | Asientos individuales, cada uno pertenece a un sector. 14.896 asientos. |
| `eventos` | Eventos (conciertos, partidos, etc.). Con fecha, hora, descripción e imagen. |
| `precios` | Tabla pivote entre eventos y sectores. Define el precio y si el sector está disponible para ese evento. |
| `estado_asientos` | Registra si un asiento está reservado (bloqueado) o vendido para un evento concreto. Se crea al reservar. |
| `entradas` | Entradas generadas tras confirmar la compra. Contienen el código QR. |
| `personal_access_tokens` | Tokens de autenticación de Sanctum. |

### Relaciones clave

```
Sector ──< Asiento ──< EstadoAsiento >── Evento
                            │
                           User
                            │
                          Entrada

Evento ─── Precio ─── Sector   (tabla pivote con precio y disponibilidad)
```

- Un **Sector** tiene muchos **Asientos**.
- Un **Evento** tiene muchos **Precios** (uno por sector).
- Un **EstadoAsiento** registra el estado (bloqueado/vendido) de un asiento para un evento concreto.
- Una **Entrada** se genera cuando el usuario confirma la compra.

---

## 5. Modelos Eloquent

### User
- Campos: `name`, `email`, `password`, `is_admin`
- Usa **SoftDeletes** (borrado lógico, no elimina de la BD)
- Método `isAdmin()`: devuelve si el usuario es administrador

### Sector
- Campos: `nombre`, `descripcion`, `activo`
- Relación: tiene muchos `Asiento`
- Scope `activos()`: filtra solo sectores con `activo = true`

### Asiento
- Campos: `sector_id`, `fila`, `numero`
- Métodos clave:
  - `nombreCompleto()`: devuelve "Sector - Fila X - Asiento Y"
  - `estaDisponibleParaEvento($eventoId)`: comprueba si no está reservado ni vendido
  - `estaReservadoParaEvento($eventoId)`: comprueba si está bloqueado y no expirado
  - `estaVendidoParaEvento($eventoId)`: comprueba si está vendido

### Evento
- Campos: `nombre`, `descripcion_corta`, `descripcion_larga`, `poster_url`, `fecha`, `hora`
- Casts: `fecha` → date, `hora` → datetime:H:i
- Usa **SoftDeletes**
- Métodos clave:
  - `sectoresDisponibles()`: devuelve sectores activos con precio disponible
  - `totalAsientosDisponibles()`: cuenta asientos libres
  - `totalEntradasVendidas()`: cuenta entradas vendidas
  - `precioDelSector($sectorId)`: devuelve el precio para un sector
- Scope `futuros()`: solo eventos con fecha >= hoy

### Precio
- Campos: `evento_id`, `sector_id`, `precio`, `disponible`
- Es la tabla pivote entre Evento y Sector
- Método `precioFormateado()`: devuelve "XX,XX €"

### EstadoAsiento
- Campos: `evento_id`, `asiento_id`, `user_id`, `estado` (bloqueado/vendido), `reservado_hasta`
- Registra reservas temporales y ventas definitivas
- El campo `reservado_hasta` marca cuándo expira la reserva (15 min desde que se crea)

### Entrada
- Campos: `user_id`, `evento_id`, `asiento_id`, `precio_pagado`, `codigo_qr`
- El `codigo_qr` es un hash MD5 único generado automáticamente al crear la entrada
- Método `informacionCompleta()`: devuelve todos los datos de la entrada para mostrar al usuario

---

## 6. Autenticación con Sanctum

Laravel Sanctum gestiona dos tipos de autenticación simultáneamente:

**Token Bearer (para la API):**
- Al hacer login, la API devuelve un token.
- El frontend lo guarda en sesión y lo incluye en las peticiones: `Authorization: Bearer {token}`.

**Sesión web (para las vistas Blade):**
- Al hacer login desde el formulario web, la sesión de PHP guarda el usuario.
- Las rutas web usan el guard `web` (cookie de sesión).

**CSRF Protection:**
- Sanctum trata las peticiones desde el mismo dominio como peticiones SPA.
- Exige el header `X-CSRF-TOKEN` en todas las peticiones POST/PUT/DELETE a la API.
- El frontend tiene un interceptor global en JavaScript que añade este header automáticamente.

---

## 7. Flujo completo de reserva y compra

```
1. Usuario elige un evento → ve sectores disponibles
2. Selecciona un sector → ve mapa de asientos (verde = libre, rojo = ocupado)
3. Hace clic en asientos → se añaden al carrito
4. Pulsa "Reservar" → POST /api/reservas por cada asiento
         └── ReservaService::reservar()
                 ├── lockForUpdate() → bloquea la fila en BD (evita doble reserva)
                 ├── Comprueba que el asiento está libre
                 └── Crea EstadoAsiento con estado='bloqueado' y reservado_hasta=now()+15min
5. Timer de 15 minutos → si no confirma, las reservas expiran
6. Pulsa "Confirmar compra" → POST /api/compras
         └── CompraService::procesarCompra()
                 ├── Comprueba que todas las reservas son del usuario y no han expirado
                 ├── Abre una transacción de BD
                 ├── Por cada reserva: crea Entrada + actualiza EstadoAsiento a 'vendido'
                 └── Confirma la transacción (o hace rollback si falla algo)
7. Usuario recibe sus entradas con código QR
```

**Por qué `lockForUpdate()`:** Evita la race condition. Si dos usuarios intentan reservar el mismo asiento a la vez, el segundo tendrá que esperar a que el primero termine. Sin esto, ambos podrían reservar el mismo asiento simultáneamente.

---

## 8. Liberación automática de reservas

Existe un comando programado que se ejecuta cada minuto:

```
LiberarReservasExpiradas (app/Console/Commands/)
    └── Llama a LiberarReservasService::liberarExpiradas()
            └── Busca EstadoAsiento con estado='bloqueado' y reservado_hasta < now()
                    └── Los elimina → el asiento vuelve a estar libre
```

Se programa en `routes/console.php` con `Schedule::command(...)->everyMinute()`.

---

## 9. Rutas de la API

### Públicas (sin autenticación)
```
GET  /api/eventos                                    → Lista eventos futuros
GET  /api/eventos/{id}                               → Detalle de un evento
GET  /api/sectores                                   → Lista sectores activos
GET  /api/eventos/{eventoId}/asientos                → Asientos de un evento
GET  /api/eventos/{eventoId}/sectores/{sectorId}/asientos → Asientos por sector
```

### Protegidas (requieren token Bearer)
```
GET    /api/user          → Datos del usuario autenticado
POST   /api/logout        → Cerrar sesión
GET    /api/reservas      → Ver mis reservas activas
POST   /api/reservas      → Crear reserva (body: evento_id, asiento_id)
DELETE /api/reservas/{id} → Cancelar reserva
POST   /api/compras       → Confirmar compra (body: reserva_ids[])
GET    /api/entradas       → Ver mis entradas
GET    /api/entradas/{id}  → Detalle de una entrada
```

### Administrador (requieren token + is_admin=true)
```
POST   /api/admin/eventos        → Crear evento
PUT    /api/admin/eventos/{id}   → Actualizar evento
DELETE /api/admin/eventos/{id}   → Eliminar evento
POST   /api/admin/sectores       → Crear sector
PUT    /api/admin/sectores/{id}  → Actualizar sector
DELETE /api/admin/sectores/{id}  → Eliminar sector
```

---

## 10. Rutas Web (vistas)

```
GET  /                          → Página principal con eventos destacados
GET  /eventos                   → Lista de todos los eventos
GET  /eventos/{id}              → Detalle del evento con selector de asientos
GET  /login                     → Formulario de login
GET  /register                  → Formulario de registro
GET  /dashboard                 → Panel del usuario (últimas entradas)
GET  /mis-entradas              → Lista de entradas compradas
GET  /entradas/{id}             → Detalle de una entrada con QR

GET  /eventos/create            → Formulario crear evento  [admin]
POST /eventos                   → Crear evento + asignar sectores [admin]
GET  /admin                     → Panel de administración  [admin]
GET  /admin/eventos/{id}/edit   → Editar evento            [admin]
GET  /admin/sectores/create     → Crear sector             [admin]
GET  /admin/sectores/{id}/edit  → Editar sector            [admin]
```

---

## 11. Controladores Web

Los controladores Web nunca acceden a la base de datos directamente. Siempre llaman al controlador API correspondiente en memoria:

```php
// Ejemplo en EntradaWebController
$response = app(EntradaController::class)->index(request());
$entradas = $response->getData(true)['data'] ?? [];
return view('entradas.index', compact('entradas'));
```

**EventoWebController::store()** es la excepción: después de crear el evento via API, crea los registros `Precio` directamente con Eloquent para asignar todos los sectores activos, porque no existe endpoint API para eso.

---

## 12. Frontend

El frontend usa **Blade** (plantillas PHP de Laravel) para el HTML y **JavaScript vanilla** (sin frameworks) para la interactividad.

### Interceptor global de fetch
En el layout principal hay un interceptor que añade automáticamente el token CSRF y el token de autenticación a todas las peticiones mutables a la API:

```javascript
window.fetch = function(url, opts) {
    if (url.startsWith('/api') && ['POST','PUT','DELETE'].includes(method)) {
        // Añade X-CSRF-TOKEN y Authorization: Bearer
    }
    return originalFetch(url, opts);
}
```

Sin esto, las peticiones POST a la API fallarían con "CSRF token mismatch".

### Páginas principales
- **Home / Eventos**: carga los eventos via `fetch('/api/eventos')` y los renderiza con JS.
- **Detalle evento**: carga sectores, permite seleccionar asientos, gestiona el carrito y el proceso de reserva/compra completamente en JS.
- **Mis entradas / Detalle entrada**: datos cargados en PHP (servidor) a través de los controladores Web.
- **Panel admin**: tablas de eventos y sectores con botones de editar/eliminar que llaman a la API.

---

## 13. Seeders (datos de prueba)

Al ejecutar `migrate:fresh --seed` se crean:

| Seeder | Qué crea |
|---|---|
| `SectorSeeder` | 71 sectores del estadio |
| `AsientoSeeder` | 14.896 asientos distribuidos en los 71 sectores |
| `UserSeeder` | 4 usuarios: 1 admin + 3 normales |
| `EventoSeeder` | 4 eventos con imágenes reales |
| `PrecioSeeder` | Precios para cada combinación evento-sector |

**Credenciales del admin:** `admin@roigarena.com` / `admin123`
**Usuarios normales:** `juan@example.com`, `maria@example.com`, `carlos@example.com` / `password`

---

## 14. Tests (49 en total)

### Feature Tests — prueban flujos HTTP completos

| Archivo | Tests | Qué prueba |
|---|---|---|
| `AuthTest` | 5 | Registro, login correcto, login fallido, logout, obtener usuario |
| `EventoTest` | 13 | CRUD eventos, listar sectores, CRUD sectores, ver asientos |
| `ReservaTest` | 6 | Reservar, no reservar ocupado, ver reservas, cancelar, no cancelar ajenas, acceso sin auth |
| `CompraTest` | 4 | Confirmar compra, reserva expirada, reserva ajena, generación QR |

### Unit Tests — prueban lógica aislada

| Archivo | Tests | Qué prueba |
|---|---|---|
| `ModeloTest` | 10 | Relaciones, soft deletes, disponibilidad asientos, métodos de modelos |
| `ReservaServiceTest` | 5 | Reservar, rechazar ocupado, cancelar, obtener activas, filtrar expiradas |
| `CompraServiceTest` | 4 | Procesar compra, rechazar expirada, compra múltiple, rollback en error |
| `LiberarReservasServiceTest` | 3 | Liberar expiradas, no liberar vendidas, liberar por usuario |

---

## 15. Despliegue

El proyecto está desplegado en **AWS EC2** usando Docker y Laravel Sail.

**Primera vez en un servidor nuevo:**
```bash
git clone <repo>
cd arena2
cp .env.example .env
docker run --rm -v "$(pwd):/var/www/html" laravelsail/php83-composer composer install --ignore-platform-reqs
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

**Resetear el entorno (datos incluidos):**
```bash
bash arena.sh
```
El script `arena.sh` (fuera de la carpeta del proyecto) hace: parar contenedores → levantar → esperar MySQL → migraciones + seeders → tests.

---

## 16. Conceptos técnicos que pueden preguntar

**¿Qué es Eloquent ORM?**
Es el sistema de Laravel para trabajar con la base de datos usando clases PHP en lugar de SQL directo. Cada tabla tiene un modelo (`Evento`, `Sector`, etc.) y las consultas se escriben de forma expresiva: `Evento::futuros()->with('precios')->get()`.

**¿Qué es una migración?**
Un archivo PHP que define la estructura de una tabla de la BD. Permiten versionar el esquema de la base de datos y recrearlo con `migrate:fresh`.

**¿Qué es un Seeder?**
Un archivo que inserta datos de prueba en la BD. Se ejecuta con `--seed` junto a las migraciones.

**¿Qué es Sanctum?**
Paquete de Laravel para autenticación. Genera tokens Bearer para APIs y también gestiona autenticación por sesión para SPAs.

**¿Qué es un Middleware?**
Código que se ejecuta antes de que llegue la petición al controlador. `IsAdmin` comprueba que el usuario tenga `is_admin=true`; si no, devuelve 403.

**¿Qué es un SoftDelete?**
En vez de eliminar el registro de la BD, se guarda la fecha de borrado en `deleted_at`. El registro sigue existiendo pero queda oculto en las consultas normales. Útil para no perder historial.

**¿Qué es una race condition y cómo se resuelve?**
Ocurre cuando dos procesos acceden al mismo recurso a la vez. En reservas: dos usuarios intentan reservar el mismo asiento simultáneamente. Se resuelve con `lockForUpdate()` que bloquea la fila en BD hasta que la transacción termina.

**¿Qué es una transacción de BD?**
Un grupo de operaciones que se ejecutan todas juntas o ninguna. Si falla algo a mitad, se hace rollback y la BD queda como estaba. Se usa en CompraService para que si falla la creación de una entrada, no se marquen las demás como vendidas.

**¿Qué es Docker / Laravel Sail?**
Docker permite ejecutar la aplicación en contenedores aislados. Laravel Sail es una interfaz de comandos sobre Docker específica para Laravel. Garantiza que el entorno es idéntico en desarrollo y producción.

**¿Por qué CSS y JS vanilla sin frameworks?**
El proyecto es backend-heavy. No necesita reactividad compleja. Blade genera el HTML en servidor y JS solo maneja la interactividad del selector de asientos y el carrito.
