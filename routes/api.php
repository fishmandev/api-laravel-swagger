<?php

use Illuminate\Support\Facades\Route;

// Custom health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'Laravel API',
        'version' => app()->version()
    ]);
}); 