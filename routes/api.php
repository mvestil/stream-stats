<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Enums\Plan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:api')->post('/subscription', [SubscriptionController::class, 'subscribe'])->name('subscribe');
Route::middleware('auth:api')->post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('sub.cancel');
