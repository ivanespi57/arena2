import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Agregar token de sesión si está disponible
const token = document.querySelector('meta[name="api-token"]');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token.getAttribute('content');
}
