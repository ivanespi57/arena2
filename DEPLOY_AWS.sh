#!/bin/bash

# ============================================
# SCRIPT DE DEPLOYMENT A AWS ACADEMY
# ============================================
#
# Este script prepara el proyecto para subir a AWS y compila allí
# Evita problemas de compilación local con WSL/npm
#

echo "🚀 === PREPARANDO DEPLOYMENT A AWS ==="
echo ""

# 1. Listar archivos nuevos
echo "📋 ARCHIVOS NUEVOS A SUBIR:"
echo ""
echo "Controllers web:"
ls -la app/Http/Controllers/Web/ 2>/dev/null || echo "  ❌ No encontrados"
echo ""

echo "Vistas Blade:"
find resources/views -type f -name "*.blade.php" 2>/dev/null | sort
echo ""

echo "📝 ARCHIVOS MODIFICADOS:"
echo "  - routes/web.php"
echo "  - resources/js/app.js"
echo "  - resources/js/bootstrap.js"
echo "  - .env (URL actualizada a AWS)"
echo ""

# 2. Preparar carpeta de deployment
echo "📦 PREPARANDO CARPETA DE DEPLOYMENT..."
mkdir -p deployment
cp -r app/Http/Controllers/Web deployment/
cp -r resources/views deployment/
cp -r resources/js deployment/
cp routes/web.php deployment/
cp .env deployment/
cp package.json deployment/
cp package-lock.json deployment/ 2>/dev/null || true

echo "✅ Archivos copiados a /deployment"
echo ""

# 3. Listar estructura
echo "📂 ESTRUCTURA PARA COPIAR A AWS:"
tree deployment -L 2 2>/dev/null || find deployment -type f | head -20
echo ""

# 4. Instrucciones
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📌 PASOS PARA SUBIR A AWS:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "1️⃣  CONECTARSE A AWS EC2:"
echo "   ssh -i ~/tu-clave.pem ec2-user@54.162.50.107"
echo ""
echo "2️⃣  EN TU MÁQUINA LOCAL, COPIAR ARCHIVOS (en PowerShell o Git Bash):"
echo ""
echo "   # Cambiar los paths según tu sistema Windows"
echo "   # Usar SCP para copiar archivos"
echo ""
echo "   scp -i 'C:\\ruta\\a\\tu\\clave.pem' -r app/Http/Controllers/Web ec2-user@54.162.50.107:/home/ec2-user/arena2/app/Http/"
echo "   scp -i 'C:\\ruta\\a\\tu\\clave.pem' -r resources/views ec2-user@54.162.50.107:/home/ec2-user/arena2/resources/"
echo "   scp -i 'C:\\ruta\\a\\tu\\clave.pem' -r resources/js ec2-user@54.162.50.107:/home/ec2-user/arena2/resources/"
echo "   scp -i 'C:\\ruta\\a\\tu\\clave.pem' routes/web.php ec2-user@54.162.50.107:/home/ec2-user/arena2/routes/"
echo "   scp -i 'C:\\ruta\\a\\tu\\clave.pem' .env ec2-user@54.162.50.107:/home/ec2-user/arena2/.env"
echo ""
echo "3️⃣  EN AWS (SSH), COMPILAR ASSETS:"
echo "   cd /home/ec2-user/arena2"
echo "   npm install"
echo "   npm run build"
echo ""
echo "4️⃣  MIGRAR BASE DE DATOS (solo si es primera vez):"
echo "   php artisan migrate --force"
echo "   php artisan db:seed --force"
echo ""
echo "5️⃣  INICIAR SERVIDOR:"
echo ""
echo "   Opción A - Modo desarrollo (puerto 8000):"
echo "   php artisan serve --host=0.0.0.0"
echo ""
echo "   Opción B - Modo producción (puerto 80, requiere sudo):"
echo "   sudo php artisan serve --host=0.0.0.0 --port=80"
echo ""
echo "   Opción C - Con nginx/apache (más recomendado):"
echo "   Configurar vhost y restart apache2/nginx"
echo ""
echo "6️⃣  ACCEDER:"
echo "   🌐 http://54.162.50.107:8000"
echo "   o"
echo "   🌐 http://54.162.50.107 (si está en puerto 80)"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ ¡Listo para deployar!"
echo ""
