import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.Pusher = Pusher;

// Log all environment variables being used
console.log("Reverb Configuration Environment Variables:", {
  VITE_REVERB_APP_KEY: import.meta.env.VITE_REVERB_APP_KEY,
  VITE_REVERB_HOST: import.meta.env.VITE_REVERB_HOST,
  VITE_REVERB_PORT: import.meta.env.VITE_REVERB_PORT,
  VITE_REVERB_SCHEME: import.meta.env.VITE_REVERB_SCHEME,
});

const echoConfig = {
  broadcaster: "reverb",
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
  wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
  enabledTransports: ["ws", "wss"],
  // Enable Pusher debugging
  enabledTransports: ['ws', 'wss'],
};

console.log("Final Echo Configuration:", echoConfig);

window.Echo = new Echo(echoConfig);

// Add connection event listeners for debugging
window.Echo.connector.pusher.connection.bind('state_change', (states) => {
  console.log('Pusher connection state changed:', states);
});

window.Echo.connector.pusher.connection.bind('connected', () => {
  console.log('Pusher connected successfully');
});

window.Echo.connector.pusher.connection.bind('failed', () => {
  console.error('Pusher connection failed');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
  console.error('Pusher connection error:', error);
});

// Log the actual WebSocket URL being constructed
const scheme = echoConfig.forceTLS ? 'wss' : 'ws';
const port = echoConfig.forceTLS ? echoConfig.wssPort : echoConfig.wsPort;
const wsUrl = `${scheme}://${echoConfig.wsHost}:${port}/app/${echoConfig.key}`;
console.log("Constructed WebSocket URL:", wsUrl);

// Test connection
window.Echo.connector.pusher.connection.bind('connected', () => {
  console.log('Subscribing to test channel...');
  window.Echo.channel('test-channel')
    .listen('.test-event', (data) => {
      console.log('Received test event:', data);
    });
});