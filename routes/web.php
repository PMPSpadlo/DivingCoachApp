<?php

use App\Http\Controllers\AthleteController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionResultController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Strona główna
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Trasy dostępne po zalogowaniu
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kluby
    Route::resource('clubs', ClubController::class);

    // Zawodnicy
    Route::resource('athletes', AthleteController::class);
    Route::get('athletes/{athlete}/results', [CompetitionResultController::class, 'showByAthlete'])->name('athletes.results');
    Route::get('athletes/{athlete}/payments', [PaymentController::class, 'showByAthlete'])->name('athletes.payments');

    // Treningi
    Route::resource('trainings', TrainingController::class);

    // Zawody
    Route::resource('competitions', CompetitionController::class);
    Route::get('competitions/{competition}/results', [CompetitionResultController::class, 'showByCompetition'])->name('competitions.results');

    // Wyniki zawodów
    Route::resource('competition-results', CompetitionResultController::class);

    // Płatności
    Route::resource('payments', PaymentController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
