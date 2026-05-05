# Frontend - Sistema de Venta de Entradas Roig Arena

## Estructura creada

### 1. **Directorio de vistas**
```
resources/views/
├── layouts/
│   └── app.blade.php          # Layout principal con navegación
├── eventos/
│   ├── index.blade.php         # Catálogo de eventos
│   └── show.blade.php          # Detalle de evento + selector de asientos
├── auth/
│   ├── login.blade.php         # Formulario de login
│   └── register.blade.php      # Formulario de registro
├── entradas/
│   ├── index.blade.php         # Mis entradas
│   └── show.blade.php          # Detalle de entrada
└── welcome.blade.php           # Página inicial (antiguo)
```

### 2. **Controladores web**
```
app/Http/Controllers/Web/
├── EventoWebController.php    # Controlador de eventos (index, show)
├── AuthWebController.php      # Controlador de autenticación
└── EntradaWebController.php   # Controlador de entradas
```

### 3. **Rutas web**
```
routes/web.php
├── GET  /                        # Home (catálogo)
├── GET  /eventos/{id}           # Detalle evento
├── GET  /register               # Formulario registro
├── POST /register               # Procesar registro
├── GET  /login                  # Formulario login
├── POST /login                  # Procesar login
├── POST /logout                 # Cerrar sesión
├── GET  /mis-entradas           # Mis entradas (autenticado)
└── GET  /entradas/{id}          # Detalle entrada (autenticado)
```

## Stack técnico

- **Backend**: Laravel 12 + Sanctum
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **HTTP Client**: Axios
- **Autenticación**: Sanctum tokens (almacenados en sesión)

## Cómo ejecutar

### 1. Compilar assets (necesario antes de ejecutar)

**Opción A: Modo desarrollo (recomendado para desarrollo)**
```bash
# En WSL o terminal del proyecto
cd /home/ivan/arena2
npm run dev
```
Esto compila CSS/JS y queda escuchando cambios en tiempo real.

**Opción B: Compilar para producción**
```bash
npm run build
```

### 2. Iniciar el servidor Laravel

```bash
# Opción A: Con Sail (Docker)
./vendor/bin/sail up

# Opción B: Sin Sail
php artisan serve
```

El frontend estará disponible en: http://localhost

## Flujo de la aplicación

### 1. **Home (Catálogo de eventos)**
- Muestra todos los eventos futuros
- Información: nombre, descripción corta, fecha, hora, asientos disponibles
- Botón "Ver más y comprar"

### 2. **Detalle de evento**
- Descripción completa del evento
- **Selector de sectores**: Grid interactivo de sectores disponibles
- **Selector de asientos**: Mapa visual de asientos por fila
  - Asientos verdes = disponibles (clickeables)
  - Asientos grises = ocupados (deshabilitados)
- **Carrito**: Resumen de asientos seleccionados y total

### 3. **Checkout (en el carrito)**
- Listado de asientos seleccionados
- Precio por asiento
- Total a pagar
- Botón "Comprar entradas" → Reserva automática → Procesa compra

### 4. **Mis entradas** (solo autenticados)
- Historial de todas las entradas compradas
- Para cada entrada:
  - Nombre del evento
  - Fecha y hora
  - Asiento y sector
  - Precio pagado
  - Código QR (para acceso)
  - Estado (válida/expirada)

### 5. **Autenticación**
- **Registro**: nombre, apellido, email, contraseña
- **Login**: email, contraseña
- **Logout**: Cierra sesión y elimina token
- Token Sanctum guardado en sesión

## Detalles técnicos importantes

### Autenticación y tokens

1. **Login/Registro**: Crea un token Sanctum y lo almacena en `session['api_token']`
2. **Bootstrap.js**: Lee el token de un meta tag y configura Axios automáticamente
3. **Meta tag**: En `layouts/app.blade.php` se incluye:
   ```html
   <meta name="api-token" content="{{ session('api_token', '') }}">
   ```
4. **Axios**: Todas las peticiones AJAX incluyen automáticamente el header `Authorization: Bearer {token}`

### Manejo de reservas y compras

1. **Seleccionar asientos**: Alpine.js maneja interactividad local
2. **Reservar**: POST `/api/reservas` → Reserva cada asiento por 15 minutos
3. **Comprar**: POST `/api/compras` → Convierte reservas en entradas
4. **Validación**: La API valida disponibilidad y permisos

### Estilo y diseño

- **Tailwind CSS v4**: Estilos modernos y responsive
- **Colores principales**: Rojo (#DC2626) para Roig Arena
- **Responsive**: Mobile-first, adapta a tablets y desktop
- **Iconos**: Emojis para mejor UX

## Archivos modificados/creados

### Nuevos archivos:
- ✅ `resources/js/app.js` - Importa Alpine.js
- ✅ `resources/js/bootstrap.js` - Configura Axios con tokens
- ✅ 8 vistas Blade en `resources/views/`
- ✅ 3 controladores web en `app/Http/Controllers/Web/`
- ✅ `routes/web.php` - Rutas completas

### Archivos actualizados:
- ✅ `package.json` - Alpine.js agregado automáticamente
- ✅ `vite.config.js` - Tailwind CSS v4 configurado
- ✅ `routes/web.php` - Todas las rutas web

## Testing del frontend

### Casos de prueba recomendados:

1. **Registro e inicio de sesión**
   - [ ] Registrarse con datos válidos
   - [ ] Intentar registrarse con email duplicado
   - [ ] Iniciar sesión con credenciales correctas
   - [ ] Iniciar sesión con credenciales incorrectas

2. **Catálogo y compra**
   - [ ] Ver lista de eventos
   - [ ] Seleccionar un evento
   - [ ] Seleccionar sector
   - [ ] Seleccionar asientos (múltiples)
   - [ ] Ver resumen en carrito
   - [ ] Procesar compra
   - [ ] Confirmar entrada creada

3. **Mis entradas**
   - [ ] Ver lista de entradas compradas
   - [ ] Ver detalle de una entrada
   - [ ] Verificar código QR

4. **Navegación**
   - [ ] Navbar muestra usuario autenticado
   - [ ] Cerrar sesión funciona
   - [ ] Redirecciones correctas después de acciones

## Problemas comunes

**P: El frontend muestra "Cargando asientos..." infinitamente**
- Verificar que la API está corriendo y accesible
- Revisar en DevTools (F12) → Network si hay errores
- Confirmar que el token se envía correctamente

**P: El carrito no actualiza**
- Alpine.js tal vez no está cargado correctamente
- Verificar en console (F12) si hay errores de JavaScript
- Asegurar que `npm run dev` está ejecutándose

**P: El login no funciona**
- Revisar credenciales en phpMyAdmin
- Confirmar que la base de datos está poblada con seeders
- Verificar que Sanctum está correctamente configurado

## Próximos pasos (opcionales)

- [ ] Agregar búsqueda/filtros de eventos
- [ ] Agregar más detalles visuales (imágenes de eventos)
- [ ] Agregar carrito persistente (localStorage)
- [ ] Generar QR como imagen
- [ ] Agregar validación del lado del cliente más robusta
- [ ] Agregar página de confirmación después de compra
- [ ] Agregar historial de cambios de estado de entrada
- [ ] Agregar formulario de contacto/soporte
