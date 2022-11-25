<?php

use App\Http\Controllers\AuthTelegramLoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FaqByTagController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ListingItemByCategoryController;
use App\Http\Controllers\ListingItemByTagController;
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
Route::get('/listings', [ListingItemController::class, 'index'])->name('listings.index');
Route::get('/listings/{category}', [ListingItemByCategoryController::class, 'index'])->name('listings.category');
Route::get('/listings/{category}/{uuid}', [ListingItemByCategoryController::class, 'show'])->name('listings.show');
Route::get('/listings/tags/{tag}', [ListingItemByTagController::class, 'index'])->name('listings-by-tag.index');

// Events
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{uuid}', [EventController::class, 'show'])->name('events.show');
// TODO: not implemented yet
//Route::get('/events/{category}', [EventByCategoryController::class, 'index'])->name('events.category');
//Route::get('/events/{category}/{uuid}', [EventByCategoryController::class, 'show'])->name('events.show');
//Route::get('/events/tags/{tag}', [EventByTagController::class, 'index'])->name('events-by-tag.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/faqs');
    })->name('dashboard');
});
