<?php

return [
    // These are the routes where CORS rules will apply
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'],
    // Allow any HTTP method (GET, POST, etc.)
    'allowed_methods' => ['*'],
    // Only allow requests from the frontend URL (set in .env or default to localhost:3000)
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
    // No special patterns for allowed origins
    'allowed_origins_patterns' => [],
    // Allow any headers in requests
    'allowed_headers' => ['*'],
    // No extra headers are exposed to the browser
    'exposed_headers' => [],
    // No caching of preflight response
    'max_age' => 0,
    // Allow cookies and credentials to be sent
    'supports_credentials' => true,
];
