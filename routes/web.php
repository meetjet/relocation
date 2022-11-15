<?php

use App\Http\Controllers\AuthTelegramLoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FaqByTagController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ListingItemController;
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
    return redirect('/listings');
})->name('welcome');

Route::get('auth/login', [AuthTelegramLoginController::class, 'show'])
    ->middleware(['guest:' . config('fortify.guard')])
    ->name('auth.login');

// FAQ
Route::get('/faqs/{slug}', [FaqController::class, 'show'])->name('faqs.show');
Route::resource('faqs', FaqController::class, ['except' => ['show']]);
Route::get('/faqs/tags/{tag}', [FaqByTagController::class, 'index'])->name('faqs-by-tag.index');

// Listings
Route::resource('/listings', ListingItemController::class);
Route::resource('/events', EventController::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/faqs');
    })->name('dashboard');
});
