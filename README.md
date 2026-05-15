# Roig Arena — Sistema de Venta de Entradas

Aplicación web completa para la gestión y venta de entradas de eventos en el Roig Arena. Desarrollada con Laravel 12, incluye backend API REST y frontend Blade con autenticación, reservas con temporizador de 15 minutos y generación de entradas con código QR.

---

## Tecnologías

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 + PHP 8.3 |
| Autenticación | Laravel Sanctum |
| Base de datos | MySQL 8.4 |
| Frontend | Blade + CSS + JS vanilla |
| Infraestructura | Docker / Laravel Sail + AWS EC2 |
| Tests | PHPUnit (49 tests) |

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
| estado_asientos | Se genera al reservar asientos |
| entradas | Se genera al confirmar compra |

---

## Instalación y puesta en marcha

### Requisitos previos
- Docker Desktop con WSL2 activado
- Git

### Primera vez (entorno nuevo)

El script `arena.sh` necesita que la carpeta `vendor/` exista para poder ejecutar Sail. Solo la primera vez hay que instalar las dependencias:

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd arena2

# 2. Copiar variables de entorno
cp .env.example .env

# 3. Instalar dependencias PHP (solo la primera vez, sin necesitar PHP local)
docker run --rm \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

# 4. Generar clave de aplicación
./vendor/bin/sail up -d && ./vendor/bin/sail artisan key:generate && ./vendor/bin/sail down
```

A partir de aquí ya puedes usar `arena.sh` con normalidad.

### Uso normal (entorno ya configurado)

```bash
bash arena.sh
```

Este script (ubicado fuera de la carpeta del proyecto) detiene los contenedores, los vuelve a levantar, espera a que MySQL esté listo, ejecuta las migraciones con seeders y lanza los tests automáticamente.

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

## Tests

```bash
./vendor/bin/sail artisan test
```

49 tests en total — 27 Feature + 22 Unit.

**Feature** (integración, prueban flujos completos via HTTP):

| Archivo | Qué comprueba |
|---|---|
| `AuthTest.php` | Registro, login, logout y obtención de datos del usuario |
| `EventoTest.php` | CRUD de eventos, listado de sectores, CRUD de sectores y consulta de asientos |
| `ReservaTest.php` | Reservar asiento, cancelar, ver reservas propias y bloquear acceso a reservas ajenas |
| `CompraTest.php` | Confirmar compra, rechazar reservas expiradas o de otro usuario y generación de QR |

**Unit** (prueban piezas de código de forma aislada):

| Archivo | Qué comprueba |
|---|---|
| `ModeloTest.php` | Relaciones Eloquent, soft deletes, métodos de los modelos y unicidad del QR |
| `ReservaServiceTest.php` | Lógica del servicio de reservas: reservar, cancelar y filtrar expiradas |
| `CompraServiceTest.php` | Lógica del servicio de compra: procesar, rollback en error y compra múltiple |
| `LiberarReservasServiceTest.php` | Liberación automática de reservas expiradas sin afectar a las vendidas |

---

## Despliegue en producción (AWS EC2)

El proyecto está desplegado en EC2 con Laravel Sail. Para actualizar tras hacer cambios:

```bash
# En el servidor EC2
cd arena2
git pull
```

Para reiniciar desde cero (migrar y sembrar todos los datos):

```bash
bash arena.sh
```
