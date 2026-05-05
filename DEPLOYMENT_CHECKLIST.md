# 📦 CHECKLIST: Archivos listos para AWS

## ✅ Archivos nuevos creados

### Controllers Web (3 archivos)
- [x] `app/Http/Controllers/Web/EventoWebController.php`
- [x] `app/Http/Controllers/Web/AuthWebController.php`
- [x] `app/Http/Controllers/Web/EntradaWebController.php`

### Vistas Blade (8 archivos)
- [x] `resources/views/layouts/app.blade.php` — Layout principal
- [x] `resources/views/eventos/index.blade.php` — Catálogo
- [x] `resources/views/eventos/show.blade.php` — Detalle + compra
- [x] `resources/views/auth/login.blade.php` — Login
- [x] `resources/views/auth/register.blade.php` — Registro
- [x] `resources/views/entradas/index.blade.php` — Mis entradas
- [x] `resources/views/entradas/show.blade.php` — Detalle entrada

### JavaScript
- [x] `resources/js/app.js` — Importa Alpine.js
- [x] `resources/js/bootstrap.js` — Configura Axios con tokens

### Rutas
- [x] `routes/web.php` — Todas las rutas del frontend

### Configuración
- [x] `.env` — Actualizado para AWS (APP_URL=http://54.162.50.107)
- [x] `package.json` — Alpine.js agregado

---

## 📋 Resumen de cambios

### Dependencias
```bash
npm install alpinejs  # ✅ Agregado
```

### Stack final
- **Backend**: Laravel 12 + Sanctum
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **HTTP Client**: Axios
- **Compilador**: Vite (npm run build)
- **Base de datos**: MySQL en AWS (54.162.50.107:3306)

---

## 🚀 Próximos pasos para DEPLOY

### 1. Desde tu máquina local (PowerShell):

```powershell
cd \\wsl.localhost\Ubuntu\home\ivan\arena2

# Copiar archivos a AWS
scp -i "C:\ruta\a\tu\clave.pem" -r app/Http/Controllers/Web ec2-user@54.162.50.107:/home/ec2-user/arena2/app/Http/
scp -i "C:\ruta\a\tu\clave.pem" -r resources/views ec2-user@54.162.50.107:/home/ec2-user/arena2/resources/
scp -i "C:\ruta\a\tu\clave.pem" -r resources/js ec2-user@54.162.50.107:/home/ec2-user/arena2/resources/
scp -i "C:\ruta\a\tu\clave.pem" routes/web.php ec2-user@54.162.50.107:/home/ec2-user/arena2/routes/
scp -i "C:\ruta\a\tu\clave.pem" .env ec2-user@54.162.50.107:/home/ec2-user/arena2/.env
scp -i "C:\ruta\a\tu\clave.pem" package.json ec2-user@54.162.50.107:/home/ec2-user/arena2/
scp -i "C:\ruta\a\tu\clave.pem" package-lock.json ec2-user@54.162.50.107:/home/ec2-user/arena2/
```

### 2. En AWS (SSH):

```bash
ssh -i tu-clave.pem ec2-user@54.162.50.107

# Compilar assets
cd arena2
npm install
npm run build

# Iniciar servidor
php artisan serve --host=0.0.0.0 --port=8000
```

### 3. Acceder en navegador:

```
http://54.162.50.107:8000
```

---

## 📚 Documentación completa

Ver archivos de guía:
- `DEPLOY_AWS_GUIA.md` — Guía paso a paso con troubleshooting
- `DEPLOY_AWS.sh` — Script de deployment automático
- `FRONTEND.md` — Documentación técnica del frontend

---

✅ **¡Frontend completamente listo para producción!**
