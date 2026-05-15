# Roig Arena — Sistema de Venta de Entradas

Aplicación web completa para la gestión y venta de entradas de eventos en el Roig Arena. Desarrollada con Laravel 12, incluye backend API REST y frontend Blade con autenticación, reservas con temporizador y generación de entradas con código QR.

---

## Tecnologías

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 + PHP 8.3 |
| Autenticación | Laravel Sanctum |
| Base de datos | MySQL 8.4 |
| Frontend | Blade + CSS + JS vanilla |
| Infraestructura | Docker / Laravel Sail + AWS EC2 |
| Tests | PHPUnit (43 tests) |

---

## Funcionalidades

**Usuario**
- Registro e inicio de sesión
- Ver eventos disponibles con imagen, fecha y descripción
- Seleccionar sector y ver mapa de asientos en tiempo real
- Reservar asientos (carrito con temporizador de 15 minutos)
- Confirmar compra y obtener entrada con código QR
- Consultar historial de entradas

**Administrador**
- Panel de gestión de eventos (crear, editar, eliminar)
- Al crear un evento se asignan automáticamente todos los sectores del estadio
- Gestión de sectores (crear, editar, activar/desactivar)

**Sistema**
- Liberación automática de reservas expiradas cada minuto
- Protección contra race condition con `lockForUpdate`
- Transacciones de base de datos en operaciones críticas

---

## Estructura del proyecto

```
app/
├── Http/Controllers/
│   ├── Auth/AuthController.php
│   ├── Web/                        # Controladores de vistas Blade
│   │   ├── AdminWebController.php
│   │   ├── AuthWebController.php
│   │   ├── EntradaWebController.php
│   │   └── EventoWebController.php
│   ├── AsientoController.php
│   ├── CompraController.php
│   ├── EntradaController.php
│   ├── EventoController.php
│   ├── ReservaController.php
│   └── SectorController.php
├── Models/                         # Evento, Sector, Asiento, Precio,
│                                   # EstadoAsiento, Entrada, User
├── Services/
│   ├── ReservaService.php
│   ├── CompraService.php
│   └── LiberarReservasService.php
└── Http/Resources/                 # 7 API Resources

database/
├── migrations/                     # 5 migraciones
└── seeders/                        # 71 sectores, 14.896 asientos, 4 eventos

resources/views/
├── layouts/app.blade.php
├── home.blade.php
├── eventos/                        # index, show, create
├── entradas/                       # index, show (con QR)
├── admin/                          # index, eventos/edit, sectores/create|edit
└── auth/                           # login, register, dashboard, profile
```

---

## Base de datos

| Tabla | Registros (inicial) |
|---|---|
| sectores | 71 |
| asientos | 14.896 |
| usuarios | 4 |
| eventos | 4 |
| precios | 284 |
| estado_asientos | 0 |
| entradas | 0 |

---

## Instalación y puesta en marcha

### Requisitos
- Docker Desktop con WSL2
- Git

### Pasos

```bash
# Clonar el repositorio
git clone <url-del-repo>
cd arena2

# Copiar variables de entorno
cp .env.example .env

# Ejecutar el script de arranque completo
bash arena.sh
```

El script `arena.sh` levanta los contenedores, ejecuta las migraciones, puebla la base de datos y verifica que todo funcione.

### Accesos locales

| Servicio | URL |
|---|---|
| Aplicación | http://localhost |
| API | http://localhost/api/eventos |
| phpMyAdmin | http://localhost:8080 |

**Credenciales de prueba**

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | admin@roigarena.com | admin123 |
| Usuario | juan@example.com | password |
| Usuario | maria@example.com | password |

---

## API REST

### Rutas públicas
```
GET  /api/eventos
GET  /api/eventos/{id}
GET  /api/sectores
GET  /api/eventos/{eventoId}/asientos
GET  /api/eventos/{eventoId}/sectores/{sectorId}/asientos
```

### Rutas autenticadas (Bearer token)
```
POST   /api/reservas
DELETE /api/reservas/{id}
POST   /api/compras
GET    /api/entradas
GET    /api/entradas/{id}
```

### Rutas de administrador
```
POST   /api/admin/eventos
PUT    /api/admin/eventos/{id}
DELETE /api/admin/eventos/{id}
POST   /api/admin/sectores
PUT    /api/admin/sectores/{id}
DELETE /api/admin/sectores/{id}
```

---

## Tests

```bash
# Ejecutar todos los tests
sail artisan test
```

43 tests en total — 31 Feature + 12 Unit — con 100% de éxito.

---

## Despliegue en producción (AWS EC2)

El proyecto está desplegado en EC2 con Laravel Sail. Para actualizar:

```bash
# En el servidor
cd arena2
git pull
```

Para reiniciar desde cero (migrar y sembrar datos):
```bash
bash arena.sh
```
