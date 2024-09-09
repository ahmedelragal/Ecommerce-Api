import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Ensure Pusher is available globally
window.Pusher = Pusher;

// Configure Echo
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
});
