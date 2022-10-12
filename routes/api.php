<?php

use App\Http\Controllers\Api\TelegramController;
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
    // Telegram webhook
    Route::post('/telegram', TelegramController::class)->middleware(JsonRequest::class);
    Route::get('/telegram', static function () {
        abort(404);
    });

    // Viber webhook
//    Route::post('/viber', [ViberController::class, 'viber']);
});
