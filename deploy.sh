#!/bin/bash

# Script para preparar el proyecto para subida a AWS

echo "🚀 Preparando proyecto para AWS..."
echo ""

# 1. Compilar assets
echo "📦 Compilando assets con Vite..."
npm run build
if [ $? -ne 0 ]; then
    echo "❌ Error compilando assets"
    exit 1
fi
echo "✅ Assets compilados"
echo ""

# 2. Mostrar archivos para subir
echo "📂 Archivos a subir a AWS:"
echo ""
echo "Nuevos controladores:"
echo "  - app/Http/Controllers/Web/EventoWebController.php"
echo "  - app/Http/Controllers/Web/AuthWebController.php"
echo "  - app/Http/Controllers/Web/EntradaWebController.php"
echo ""
echo "Nuevas vistas:"
echo "  - resources/views/layouts/app.blade.php"
echo "  - resources/views/eventos/index.blade.php"
echo "  - resources/views/eventos/show.blade.php"
echo "  - resources/views/auth/login.blade.php"
echo "  - resources/views/auth/register.blade.php"
echo "  - resources/views/entradas/index.blade.php"
echo "  - resources/views/entradas/show.blade.php"
echo ""
echo "Modificados:"
echo "  - routes/web.php"
echo "  - resources/js/app.js"
echo "  - resources/js/bootstrap.js"
echo "  - .env"
echo ""
echo "Assets compilados:"
echo "  - public/build/ (toda la carpeta)"
echo ""

echo "📋 Pasos para subir a AWS:"
echo ""
echo "1. Conectarse a AWS EC2:"
echo "   ssh -i tu-key.pem ec2-user@54.162.50.107"
echo ""
echo "2. Copiar archivos (desde tu máquina local):"
echo "   scp -i tu-key.pem -r app/Http/Controllers/Web ec2-user@54.162.50.107:/home/ec2-user/arena2/app/Http/"
echo "   scp -i tu-key.pem -r resources/views ec2-user@54.162.50.107:/home/ec2-user/arena2/resources/"
echo "   scp -i tu-key.pem -r resources/js ec2-user@54.162.50.107:/home/ec2-user/arena2/resources/"
echo "   scp -i tu-key.pem -r public/build ec2-user@54.162.50.107:/home/ec2-user/arena2/public/"
echo "   scp -i tu-key.pem routes/web.php ec2-user@54.162.50.107:/home/ec2-user/arena2/routes/"
echo "   scp -i tu-key.pem .env ec2-user@54.162.50.107:/home/ec2-user/arena2/.env"
echo ""
echo "3. En AWS, ejecutar migraciones y seeders (si es primera vez):"
echo "   php artisan migrate --force"
echo "   php artisan db:seed --force"
echo ""
echo "4. En AWS, iniciar el servidor:"
echo "   php artisan serve --host=0.0.0.0 --port=80"
echo ""
echo "✅ ¡Listo para subir!"
