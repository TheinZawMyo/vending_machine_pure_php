<?php

return [
    'secret_key' => getenv('JWT_SECRET') ?: 'aP9s8f7g6H2j1K3l4M5n6O7p8Q9r0S1tU2v3W4x5Y6z7A8b9C0d1E2f3G4h5I6j7',
    'algorithm'  => 'HS256',
    'expiration' => 3600, // seconds
];
