// Bootstrap de la aplicación
// Este archivo puede usarse para configuraciones globales de JavaScript

// Setup CSRF token globalmente si es necesario
const token = document.querySelector('meta[name="csrf-token"]')?.content;
if (token) {
    window.csrfToken = token;
}

// Logging simple
console.log('Roig Arena - Blade Frontend Loaded');
