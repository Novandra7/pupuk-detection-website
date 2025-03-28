/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

if (import.meta.env.VITE_BROADCAST_DRIVER !== 'log') {
    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        cluster: import.meta.env.VITE_REVERB_APP_CLUSTER ?? 'mt1',
        wsHost: import.meta.env.VITE_REVERB_HOST ? import.meta.env.VITE_REVERB_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}

/**
 * Register the service worker if it's supported by the user browser
 * this will allow us to cache assets and page for offline use
 * and provide a faster loading experience for users
 */

// if ('serviceWorker' in navigator) {
//     if (navigator.serviceWorker.controller) {
//         // The service worker is already controlling this page
//     } else {
//         // Register the service worker
//         navigator.serviceWorker.register('/sw.js')
//             .then((registration) => {
//                 // Registration was successful
//             })
//             .catch((error) => {
//                 console.error('ServiceWorker registration failed:', error);
//             });
//     }
// }