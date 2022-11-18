<?php

use App\Http\Controllers\Api\ArmeniaTelegramController;
use App\Http\Controllers\Api\DefaultTelegramController;
use App\Http\Controllers\Api\ThailandTelegramController;
use App\Http\Middleware\JsonRequest;
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

Route::prefix('bots')->group(function () {
    // Telegram webhooks
    Route::group([
        'prefix' => 'telegram',
        'middleware' => [JsonRequest::class],
    ], static function () {
        Route::post('default', DefaultTelegramController::class);
        Route::post('armenia', ArmeniaTelegramController::class);
        Route::post('thailand', ThailandTelegramController::class);
    });

    // Viber webhook
//    Route::post('/viber', [ViberController::class, 'viber']);

    Route::fallback(static function () {
        abort(404);
    });
});
