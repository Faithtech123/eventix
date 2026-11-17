<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Organizer\CheckInController;
use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\ManageEventController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [EventController::class, 'index'])
    ->name('home');
Route::get('/events/{event}', [EventController::class, 'show'])
    ->name('events.show');

// Guests only
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Any signed-in user
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::post('/events/{event}/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');
    Route::get('/orders/{order}', [CheckoutController::class, 'show'])
        ->name('orders.show');

    Route::get('/my-tickets', [TicketController::class, 'index'])
        ->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->name('tickets.show');
});

// Organizers and admins
Route::middleware(['auth', 'role:organizer,admin'])
    ->prefix('organizer')
    ->name('organizer.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/events/create', [ManageEventController::class, 'create'])
            ->name('events.create');
        Route::post('/events', [ManageEventController::class, 'store'])
            ->name('events.store');
        Route::get('/events/{event}/edit', [ManageEventController::class, 'edit'])
            ->name('events.edit');
        Route::put('/events/{event}', [ManageEventController::class, 'update'])
            ->name('events.update');
        Route::delete('/events/{event}', [ManageEventController::class, 'destroy'])
            ->name('events.destroy');
        Route::get('/events/{event}/attendees', [ManageEventController::class, 'attendees'])
            ->name('events.attendees');

        Route::get('/events/{event}/check-in', [CheckInController::class, 'show'])
            ->name('checkin.show');
        Route::post('/events/{event}/check-in', [CheckInController::class, 'verify'])
            ->name('checkin.verify');
    });

// Admin only
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])
            ->name('dashboard');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])
            ->name('users.role');
    });
