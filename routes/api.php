<?php

use Illuminate\Support\Facades\Route;
use Upcoach\UpstartForLaravel\Http\Controllers\InstallController;
use Upcoach\UpstartForLaravel\Http\Controllers\WebhookController;

Route::middleware('api')
->prefix('api')
->group(function () {
    Route::post('/upcoach-install', InstallController::class);
    Route::post('/upcoach-webhooks', WebhookController::class);
});
