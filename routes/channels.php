<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('help-requests', function ($user) {
    return true;
});
