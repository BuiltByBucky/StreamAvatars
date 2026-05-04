import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
Pusher.logToConsole = true;

const tls      = import.meta.env.VITE_REVERB_SCHEME === 'https';
const host     = import.meta.env.VITE_REVERB_HOST;
const port     = Number(import.meta.env.VITE_REVERB_PORT);

const echo = new Echo({
    broadcaster:        'reverb',
    key:                import.meta.env.VITE_REVERB_APP_KEY,
    wsHost:             host,
    wsPort:             port,
    wssPort:            port,
    forceTLS:           tls,
    enabledTransports:  tls ? ['wss'] : ['ws'],
});

export default echo;
