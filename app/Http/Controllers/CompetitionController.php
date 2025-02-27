<?php

// CompetitionController.php
namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Club;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CompetitionController extends Controller
{
    public function index(Request $request)
    {
        $userClubs = auth()->user()->clubs->pluck('id');
        $clubId = $request->input('club_id');
        $query = Competition::query();

        if ($clubId && in_array($clubId, $userClubs->toArray())) {
            $query->where('club_id', $clubId);
            $selectedClub = Club::find($clubId);
        } else {
            $query->whereIn('club_id', $userClubs);
            $selectedClub = null;
        }

        // Filtrowanie po typie zawodów
        if ($request->has('type') && in_array($request->type, ['platform', 'springboard', 'synchro'])) {
            $query->where('type', $request->type);
        }

        // Filtrowanie po poziomie zawodów
        if ($request->has('level') && in_array($request->level, ['regional', 'national', 'international'])) {
            $query->where('level', $request->level);
        }

        // Filtrowanie po statusie (przyszłe/przeszłe)
        if ($request->has('status')) {
            if ($request->status === 'upcoming') {
                $query->where('date', '>=', Carbon::today());
            } elseif ($request->status === 'past') {
                $query->where('date', '<', Carbon::today());
            }
        }

        // Sortowanie (domyślnie po dacie malejąco)
        $query->orderBy('date', 'desc');

        $competitions = $query->with('club')->paginate(10);
        $clubs = auth()->user()->clubs;

        return Inertia::render('Competitions/Index', [
            'competitions' => $competitions,
            'clubs' => $clubs,
            'filters' => [
                'club_id' => $clubId,
                'type' => $request->input('type'),
                'level' => $request->input('level'),
                'status' => $request->input('status'),
            ],
            'selectedClub' => $selectedClub,
        ]);
    }

    public function create(Request $request)
    {
        $clubs = auth()->user()->clubs;
        $preselectedClubId = $request->input('club_id');

        // Sprawdzamy czy preselectedClubId należy do klubów użytkownika
        if ($preselectedClubId && !$clubs->contains('id', $preselectedClubId)) {
            abort(403);
        }

        return Inertia::render('Competitions/Create', [
            'clubs' => $clubs,
            'preselectedClubId' => $preselectedClubId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'club_id' => 'required|exists:clubs,id',
            'judge_panel' => 'nullable|string|max:255',
            'type' => 'required|in:platform,springboard,synchro',
            'level' => 'required|in:regional,national,international',
        ]);

        // Sprawdź czy użytkownik ma dostęp do wybranego klubu
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($validated['club_id'], $userClubs)) {
            abort(403);
        }

        $competition = Competition::create($validated);

        return redirect()->route('competitions.show', $competition)
            ->with('success', 'Zawody zostały dodane pomyślnie.');
    }

    public function show(Competition $competition)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu zawodów
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($competition->club_id, $userClubs)) {
            abort(403);
        }

        // Załaduj relacje
        $competition->load([
            'club',
            'results' => function($query) {
                $query->with('athlete')->orderBy('rank');
            }
        ]);

        // Pobierz zawodników z klubu, którzy mogą być dodani do zawodów
        $clubAthletes = Athlete::where('club_id', $competition->club_id)
            ->orderBy('last_name')
            ->get();

        return Inertia::render('Competitions/Show', [
            'competition' => $competition,
            'clubAthletes' => $clubAthletes,
        ]);
    }

    public function edit(Competition $competition)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu zawodów
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($competition->club_id, $userClubs)) {
            abort(403);
        }

        return Inertia::render('Competitions/Edit', [
            'competition' => $competition,
            'club' => $competition->club,
        ]);
    }

    public function update(Request $request, Competition $competition)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu zawodów
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($competition->club_id, $userClubs)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'judge_panel' => 'nullable|string|max:255',
            'type' => 'required|in:platform,springboard,synchro',
            'level' => 'required|in:regional,national,international',
        ]);

        $competition->update($validated);

        return redirect()->route('competitions.show', $competition)
            ->with('success', 'Dane zawodów zostały zaktualizowane.');
    }

    public function destroy(Competition $competition)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu zawodów
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($competition->club_id, $userClubs)) {
            abort(403);
        }

        $competition->delete();

        return redirect()->route('competitions.index')
            ->with('success', 'Zawody zostały usunięte.');
    }
}
