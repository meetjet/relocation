<?php

use App\Http\Controllers\Api\TelegramController;
use App\Http\Middleware\LogExchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('bots')->middleware(LogExchange::class)->group(function () {
    // Telegram webhook
    Route::post('/telegram', TelegramController::class);

    // Viber webhook
//    Route::post('/viber', [ViberController::class, 'viber']);
});
