// src/services/echo.js
// This file sets up Laravel Echo for real-time events using Pusher.
// It connects to the backend and uses the user's token for authentication.

import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// Make Pusher available globally (needed by Echo)
window.Pusher = Pusher

const echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
  forceTLS: true,
  auth: {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  },
})

export default echo
