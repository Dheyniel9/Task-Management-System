// routes/channels.php
<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// User-specific channel for task updates
Broadcast::channel('user.{userId}', function (User $user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Admin channel for system-wide updates
Broadcast::channel('admin', function (User $user) {
    return $user->isAdmin();
});
