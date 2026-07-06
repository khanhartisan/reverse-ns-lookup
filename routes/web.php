<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\LookupController::class, 'index'])->name('lookup');
