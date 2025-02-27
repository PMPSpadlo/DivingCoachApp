<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Pobieramy kluby, do których należy zalogowany użytkownik
        $userClubs = auth()->user()->clubs->pluck('id');

        // Statystyki dla dashboardu
        $stats = [
            'athletes' => [
                'total' => Athlete::whereIn('club_id', $userClubs)->count(),
                'junior' => Athlete::whereIn('club_id', $userClubs)->where('age_category', 'junior')->count(),
                'senior' => Athlete::whereIn('club_id', $userClubs)->where('age_category', 'senior')->count(),
            ],
            'trainings' => [
                'total' => Training::whereIn('club_id', $userClubs)->count(),
                'upcoming' => Training::whereIn('club_id', $userClubs)->where('date', '>', Carbon::now())->count(),
            ],
            'competitions' => [
                'total' => Competition::whereIn('club_id', $userClubs)->count(),
                'upcoming' => Competition::whereIn('club_id', $userClubs)->where('date', '>', Carbon::now())->count(),
            ],
            'payments' => [
                'total' => Payment::whereIn('club_id', $userClubs)->where('status', 'paid')->sum('amount'),
                'pending' => Payment::whereIn('club_id', $userClubs)->where('status', 'pending')->sum('amount'),
                'overdue' => Payment::whereIn('club_id', $userClubs)->where('status', 'overdue')->sum('amount'),
            ],
        ];

        // Pobieramy nadchodzące treningi
        $upcomingTrainings = Training::whereIn('club_id', $userClubs)
            ->where('date', '>', Carbon::now())
            ->orderBy('date')
            ->with('trainer', 'club')
            ->limit(5)
            ->get();

        // Pobieramy nadchodzące zawody
        $upcomingCompetitions = Competition::whereIn('club_id', $userClubs)
            ->where('date', '>', Carbon::now())
            ->orderBy('date')
            ->with('club')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'upcomingTrainings' => $upcomingTrainings,
            'upcomingCompetitions' => $upcomingCompetitions,
        ]);
    }
}
