# 🖼️ Cómo Subir Imágenes de Eventos

## Ubicación de las imágenes

Las imágenes de los eventos deben almacenarse en:

```
storage/app/public/eventos/
```

## Pasos para subir imágenes

### 1. **En desarrollo local** (tu PC)
```bash
# Crear el directorio si no existe
mkdir -p storage/app/public/eventos

# Copiar tus imágenes aquí
cp /ruta/a/tu/imagen.jpg storage/app/public/eventos/
```

### 2. **En AWS (EC2)**
```bash
# Conectar por SSH/Session Manager a tu instancia
# Navegar a la carpeta
cd ~/arena2

# Crear el directorio
mkdir -p storage/app/public/eventos

# Opción A: Subir archivos via SCP (desde tu PC)
scp -i tu-clave.pem imagen.jpg ec2-user@tu-ip:/home/ubuntu/arena2/storage/app/public/eventos/

# Opción B: Subir via SFTP o copiar archivos manualmente
```

### 3. **Crear enlace simbólico** (MUY IMPORTANTE)
```bash
php artisan storage:link
```

Esto crea un enlace de `public/storage/` a `storage/app/public/`

## En la base de datos

Cuando crees un evento, en el campo `poster_url` pon:

```
/storage/eventos/nombre-imagen.jpg
```

O en la vista, la ruta será:

```blade
{{ asset('storage/eventos/nombre-imagen.jpg') }}
```

## Ejemplo de almacenamiento

```
storage/app/public/eventos/
├── concierto-rock-2026.jpg
├── final-copa-del-rey.jpg
└── festival-electronica.jpg
```

## Dentro del código (Laravel)

Para guardar una imagen subida por formulario:

```php
if ($request->hasFile('poster')) {
    $path = $request->file('poster')->store('eventos', 'public');
    $evento->poster_url = '/storage/' . $path;
}
```

---

**⚠️ Recuerda:** Si cambias las imágenes en AWS, los cambios se verán después de actualizar la página (Ctrl+F5 para limpiar caché).
