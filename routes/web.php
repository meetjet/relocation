<?php

use App\Http\Controllers\FaqController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/faqs');
})->name('welcome');

Route::get('/faqs/{slug}', [FaqController::class, 'show'])->name('faqs.show');
Route::resource('faqs', FaqController::class, ['except' => ['show']]);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/faqs');
    })->name('dashboard');
});
