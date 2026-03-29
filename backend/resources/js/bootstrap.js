import axios from 'axios';
window.axios = axios;

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: 8080,
    forceTLS: false,
    disableStats: true,

    // ✅ Required for pusher-js (even if not used by Reverb)
    cluster: import.meta.env.VITE_REVERB_APP_CLUSTER ?? 'mt1',
     // ✅ Important: correct auth endpoint
        authEndpoint: `${import.meta.env.VITE_APP_URL}/broadcasting/auth`,
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    },
});


// Debug logs
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('✅ Echo connected to Reverb');
});
window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('❌ Echo error:', err);
});

// Optional: if you have channel subscriptions in echo.js
// import './echo';
