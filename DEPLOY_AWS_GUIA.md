# 🚀 GUÍA COMPLETA: DEPLOY A AWS ACADEMY

## 📋 Requisitos previos

- ✅ Clave PEM descargada (archivo .pem de AWS)
- ✅ IP de EC2: **54.162.50.107**
- ✅ Usuario: **ec2-user** (o el que uses en tu instancia)
- ✅ Backend ya está en AWS con BD configurada

---

## 🔧 PASO 1: Preparar archivos para copiar

Los archivos **nuevos** que subimos son:

```
app/Http/Controllers/Web/
├── EventoWebController.php
├── AuthWebController.php
└── EntradaWebController.php

resources/views/
├── layouts/app.blade.php
├── eventos/
│   ├── index.blade.php
│   └── show.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
└── entradas/
    ├── index.blade.php
    └── show.blade.php

resources/js/
├── app.js (modificado)
└── bootstrap.js (modificado)

routes/web.php (modificado)
.env (actualizado con IP de AWS)
```

---

## 🔑 PASO 2: Conectar a AWS via SSH

### En PowerShell:

```powershell
# Navegar a donde tienes la clave
cd C:\Users\[TuUsuario]\Downloads

# Conectar a AWS
ssh -i nombre-de-tu-clave.pem ec2-user@54.162.50.107
```

Si pide contraseña, es que la clave no está bien configurada. En AWS Academy, la clave debe ser sin contraseña.

---

## 📤 PASO 3: Copiar archivos a AWS

### Opción A: Copiar usando SCP desde PowerShell (tu máquina local)

Abre **PowerShell** en tu máquina (NO en AWS), en la carpeta `arena2`:

```powershell
# Cambiar a la carpeta del proyecto
cd \\wsl.localhost\Ubuntu\home\ivan\arena2

# Copiar controladores
scp -i "C:\ruta\a\tu\clave.pem" -r app/Http/Controllers/Web ec2-user@54.162.50.107:/home/ec2-user/arena2/app/Http/

# Copiar vistas
scp -i "C:\ruta\a\tu\clave.pem" -r resources/views ec2-user@54.162.50.107:/home/ec2-user/arena2/resources/

# Copiar JavaScript
scp -i "C:\ruta\a\tu\clave.pem" -r resources/js ec2-user@54.162.50.107:/home/ec2-user/arena2/resources/

# Copiar rutas web
scp -i "C:\ruta\a\tu\clave.pem" routes/web.php ec2-user@54.162.50.107:/home/ec2-user/arena2/routes/

# Copiar .env
scp -i "C:\ruta\a\tu\clave.pem" .env ec2-user@54.162.50.107:/home/ec2-user/arena2/.env

# Copiar package.json (importante para npm)
scp -i "C:\ruta\a\tu\clave.pem" package.json ec2-user@54.162.50.107:/home/ec2-user/arena2/
scp -i "C:\ruta\a\tu\clave.pem" package-lock.json ec2-user@54.162.50.107:/home/ec2-user/arena2/
```

### Opción B: Crear archivo .zip y subir

```powershell
# Crear zip con archivos
Compress-Archive -Path app/Http/Controllers/Web, resources/views, resources/js, routes/web.php, .env -DestinationPath arena2-frontend.zip

# Copiar zip a AWS
scp -i "C:\ruta\a\tu\clave.pem" arena2-frontend.zip ec2-user@54.162.50.107:/home/ec2-user/

# En AWS (ssh), descomprimir
# unzip /home/ec2-user/arena2-frontend.zip -d /home/ec2-user/arena2
```

---

## ⚙️ PASO 4: En AWS - Compilar assets

SSH a tu instancia:

```bash
ssh -i tu-clave.pem ec2-user@54.162.50.107
```

Una vez dentro de AWS:

```bash
# Ir a la carpeta del proyecto
cd arena2

# Instalar dependencias Node
npm install

# Compilar assets (Vite + Tailwind)
npm run build

# Verificar que public/build fue creado
ls -la public/build
```

---

## 🗄️ PASO 5: Base de datos (solo si es primera vez)

```bash
# Ejecutar migraciones
php artisan migrate --force

# Ejecutar seeders (para llenar eventos, sectores, etc.)
php artisan db:seed --force

# Verificar conexión a BD
php artisan tinker
# > DB::connection()->getPdo();
# Si no hay error, ¡BD conectada!
```

---

## 🚀 PASO 6: Iniciar el servidor

### Opción A: Modo desarrollo (recomendado para testing)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Accede a: **http://54.162.50.107:8000**

### Opción B: Modo producción en puerto 80 (requiere sudo)

```bash
sudo php artisan serve --host=0.0.0.0 --port=80
```

Accede a: **http://54.162.50.107**

> ⚠️ Nota: Para puerto 80 necesitas permisos de root. Si no funciona, usa puerto 8000.

### Opción C: Con Apache/Nginx (más recomendado)

Si tu instancia ya tiene Apache/Nginx configurado:

```bash
# Copiar archivo .htaccess o configurar vhost
# Reiniciar Apache
sudo systemctl restart apache2
# o Nginx
sudo systemctl restart nginx
```

---

## ✅ Verificación

Una vez que el servidor está arriba:

### 1. Comprobar que funciona

```bash
# En tu máquina local, en PowerShell/Bash:
curl http://54.162.50.107:8000
# Debe devolver HTML de la página inicio
```

### 2. Acceder en navegador

- **Home (catálogo)**: http://54.162.50.107:8000
- **Login**: http://54.162.50.107:8000/login
- **Registro**: http://54.162.50.107:8000/register

### 3. Probar login

1. Registrarse con un usuario nuevo
2. Login con esas credenciales
3. Ver catálogo de eventos
4. Hacer clic en un evento
5. Seleccionar asientos y comprar

---

## 🐛 Troubleshooting

### ❌ "Permission denied" al copiar con SCP

**Solución**: Asegúrate de que la clave .pem tiene permisos correctos:

```powershell
# En PowerShell, cambiar permisos de la clave
icacls "C:\ruta\a\tu\clave.pem" /reset /T
```

### ❌ "Comando npm no encontrado"

**Solución**: Instalar Node.js en AWS

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
source ~/.bashrc
nvm install node
npm --version
```

### ❌ "CORS error" en navegador

**Solución**: Verificar `config/cors.php` en el backend:

```php
'allowed_origins' => ['*'],  // O la IP específica
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### ❌ "Cannot GET /"

**Solución**: Las rutas web no se están cargando. Verificar:

1. `routes/web.php` está actualizado
2. Los controladores en `app/Http/Controllers/Web/` existen
3. Las vistas en `resources/views/` existen

```bash
# Verificar que los archivos existen
ls -la app/Http/Controllers/Web/
ls -la resources/views/
```

### ❌ Base de datos no conecta

**Solución**: Verificar variables de .env

```bash
# Ver variables
cat .env | grep DB_

# Deben ser:
# DB_HOST=54.162.50.107
# DB_DATABASE=arena2
# DB_USERNAME=arena2_user
# DB_PASSWORD=Arena2Pass2024!Student
```

---

## 📞 Support

Si tienes problemas:

1. Revisar logs en AWS:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Revisar en navegador (F12 → Console):
   ```
   Errores de JavaScript o Network requests fallidas
   ```

3. Verificar que API está accesible:
   ```bash
   curl http://54.162.50.107:8000/api/eventos
   ```

---

## ✨ ¡Listo!

Una vez que todo funciona, tendrás:

- ✅ Frontend Blade + Alpine.js + Tailwind
- ✅ API funcionando en AWS
- ✅ Base de datos en AWS
- ✅ Todo accesible desde una sola IP: **http://54.162.50.107**

¡A disfrutar! 🎉
