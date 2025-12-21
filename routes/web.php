<?php

use Illuminate\Support\Facades\Route;
use Laravilt\Users\Http\Controllers\ImpersonationController;

Route::middleware(['web', 'auth'])->prefix('laravilt/users')->name('laravilt.users.')->group(function () {
    // Impersonation routes
    Route::post('/impersonate/{user}', [ImpersonationController::class, 'impersonate'])
        ->name('impersonate');

    Route::post('/stop-impersonation', [ImpersonationController::class, 'stopImpersonation'])
        ->name('stop-impersonation');
});
