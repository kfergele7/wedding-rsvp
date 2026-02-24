<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PartyController;
use App\Http\Controllers\Admin\RsvpController as AdminRsvpController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\RsvpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::get('/rsvp/{code?}', [PublicSiteController::class, 'rsvp'])->name('rsvp.page');
Route::post('/rsvp/lookup', [RsvpController::class, 'lookup'])->middleware('throttle:20,1')->name('rsvp.lookup');
Route::post('/rsvp/{code}', [RsvpController::class, 'save'])->middleware('throttle:20,1')->name('rsvp.save');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'app'])->name('dashboard');
    Route::get('/parties', fn () => app(DashboardController::class)->app('parties'))->name('parties.page');
    Route::get('/rsvps', fn () => app(DashboardController::class)->app('rsvps'))->name('rsvps.page');
    Route::get('/content', fn () => app(DashboardController::class)->app('content'))->name('content.page');

    Route::get('/api/dashboard', [DashboardController::class, 'stats'])->name('api.dashboard');
    Route::get('/api/content', [ContentController::class, 'show'])->name('api.content.show');
    Route::put('/api/content', [ContentController::class, 'update'])->name('api.content.update');
    Route::post('/api/content/image', [ContentController::class, 'uploadImage'])->name('api.content.image');

    Route::get('/api/parties', [PartyController::class, 'index'])->name('api.parties.index');
    Route::get('/api/parties/generate-code', [PartyController::class, 'generateCode'])->name('api.parties.generate-code');
    Route::post('/api/parties', [PartyController::class, 'store'])->name('api.parties.store');
    Route::get('/api/parties/export', [PartyController::class, 'export'])->name('api.parties.export');
    Route::post('/api/parties/import', [PartyController::class, 'import'])->name('api.parties.import');
    Route::get('/api/parties/{party}', [PartyController::class, 'show'])->name('api.parties.show');
    Route::put('/api/parties/{party}', [PartyController::class, 'update'])->name('api.parties.update');
    Route::delete('/api/parties/{party}', [PartyController::class, 'destroy'])->name('api.parties.destroy');

    Route::post('/api/parties/{party}/guests', [PartyController::class, 'storeGuest'])->name('api.guests.store');
    Route::put('/api/guests/{guest}', [PartyController::class, 'updateGuest'])->name('api.guests.update');
    Route::delete('/api/guests/{guest}', [PartyController::class, 'destroyGuest'])->name('api.guests.destroy');

    Route::get('/api/rsvps', [AdminRsvpController::class, 'index'])->name('api.rsvps.index');
    Route::put('/api/rsvps/{party}', [AdminRsvpController::class, 'update'])->name('api.rsvps.update');
    Route::get('/api/rsvps/export', [AdminRsvpController::class, 'export'])->name('api.rsvps.export');
});
