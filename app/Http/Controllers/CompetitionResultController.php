<?php

// CompetitionResultController.php
namespace App\Http\Controllers;

use App\Models\CompetitionResult;
use App\Models\Competition;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompetitionResultController extends Controller
{
    public function index(Request $request)
    {
        $userClubs = auth()->user()->clubs->pluck('id');
        $clubId = $request->input('club_id');

        $query = CompetitionResult::query()
            ->with(['competition', 'athlete.club'])
            ->whereHas('competition', function ($q) use ($userClubs, $clubId) {
                $q->whereIn('club_id', $clubId ? [$clubId] : $userClubs);
            });

        // Filtrowanie po zawodach
        if ($request->has('competition_id')) {
            $query->where('competition_id', $request->competition_id);
        }

        // Filtrowanie po zawodniku
        if ($request->has('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        // Sortowanie
        $query->orderBy('created_at', 'desc');

        $results = $query->paginate(15);
        $clubs = auth()->user()->clubs;

        // Pobierz listę zawodów i zawodników do filtrowania
        $competitions = Competition::whereIn('club_id', $userClubs)->orderBy('date', 'desc')->get();
        $athletes = Athlete::whereIn('club_id', $userClubs)->orderBy('last_name')->get();

        return Inertia::render('CompetitionResults/Index', [
            'results' => $results,
            'clubs' => $clubs,
            'competitions' => $competitions,
            'athletes' => $athletes,
            'filters' => [
                'club_id' => $clubId,
                'competition_id' => $request->input('competition_id'),
                'athlete_id' => $request->input('athlete_id'),
            ],
        ]);
    }

    public function create(Request $request)
    {
        $userClubs = auth()->user()->clubs->pluck('id');
        $competitionId = $request->input('competition_id');
        $athleteId = $request->input('athlete_id');

        // Pobierz zawody, do których użytkownik ma dostęp
        $competitions = Competition::whereIn('club_id', $userClubs)->orderBy('date', 'desc')->get();

        // Jeśli podano competition_id, sprawdź czy użytkownik ma do niego dostęp
        $competition = null;
        $clubAthletes = collect();

        if ($competitionId) {
            $competition = Competition::whereIn('club_id', $userClubs)->find($competitionId);
            if (!$competition) {
                abort(403);
            }

            // Pobierz zawodników z klubu zawodów
            $clubAthletes = Athlete::where('club_id', $competition->club_id)->orderBy('last_name')->get();
        }

        // Jeśli podano athlete_id, sprawdź czy zawodnik należy do klubu użytkownika
        $athlete = null;
        if ($athleteId) {
            $athlete = Athlete::whereIn('club_id', $userClubs)->find($athleteId);
            if (!$athlete) {
                abort(403);
            }
        }

        return Inertia::render('CompetitionResults/Create', [
            'competitions' => $competitions,
            'selectedCompetition' => $competition,
            'clubAthletes' => $clubAthletes,
            'selectedAthlete' => $athlete,
        ]);
    }

    public function store(Request $request)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        $validated = $request->validate([
            'competition_id' => 'required|exists:competitions,id',
            'athlete_id' => 'required|exists:athletes,id',
            'score' => 'required|numeric|between:0,100',
            'dive_type' => 'nullable|string|max:255',
            'difficulty_level' => 'nullable|string|max:255',
            'rank' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Sprawdź czy zawody należą do klubu użytkownika
        $competition = Competition::find($validated['competition_id']);
        if (!$competition || !in_array($competition->club_id, $userClubs->toArray())) {
            abort(403);
        }

        // Sprawdź czy zawodnik należy do klubu użytkownika
        $athlete = Athlete::find($validated['athlete_id']);
        if (!$athlete || !in_array($athlete->club_id, $userClubs->toArray())) {
            abort(403);
        }

        $result = CompetitionResult::create($validated);

        return redirect()->route('competition-results.show', $result)
            ->with('success', 'Wynik zawodów został dodany pomyślnie.');
    }

    public function show(CompetitionResult $competitionResult)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy wyniki należą do klubu użytkownika
        $competition = $competitionResult->competition;
        if (!in_array($competition->club_id, $userClubs->toArray())) {
            abort(403);
        }

        // Załaduj relacje
        $competitionResult->load(['competition', 'athlete']);

        return Inertia::render('CompetitionResults/Show', [
            'result' => $competitionResult,
        ]);
    }

    public function edit(CompetitionResult $competitionResult)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy wyniki należą do klubu użytkownika
        $competition = $competitionResult->competition;
        if (!in_array($competition->club_id, $userClubs->toArray())) {
            abort(403);
        }

        // Załaduj relacje
        $competitionResult->load(['competition', 'athlete']);

        // Pobierz zawodników z klubu zawodów
        $clubAthletes = Athlete::where('club_id', $competition->club_id)->orderBy('last_name')->get();

        return Inertia::render('CompetitionResults/Edit', [
            'result' => $competitionResult,
            'clubAthletes' => $clubAthletes,
        ]);
    }

    public function update(Request $request, CompetitionResult $competitionResult)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy wyniki należą do klubu użytkownika
        $competition = $competitionResult->competition;
        if (!in_array($competition->club_id, $userClubs->toArray())) {
            abort(403);
        }

        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'score' => 'required|numeric|between:0,100',
            'dive_type' => 'nullable|string|max:255',
            'difficulty_level' => 'nullable|string|max:255',
            'rank' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Sprawdź czy zawodnik należy do klubu zawodów
        $athlete = Athlete::find($validated['athlete_id']);
        if (!$athlete || $athlete->club_id != $competition->club_id) {
            abort(403);
        }

        $competitionResult->update($validated);

        return redirect()->route('competition-results.show', $competitionResult)
            ->with('success', 'Wynik zawodów został zaktualizowany pomyślnie.');
    }

    public function destroy(CompetitionResult $competitionResult)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy wyniki należą do klubu użytkownika
        $competition = $competitionResult->competition;
        if (!in_array($competition->club_id, $userClubs->toArray())) {
            abort(403);
        }

        $competitionResult->delete();

        return redirect()->route('competition-results.index')
            ->with('success', 'Wynik zawodów został usunięty.');
    }

    // Dodatkowe metody
    public function showByAthlete(Athlete $athlete)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy zawodnik należy do klubu użytkownika
        if (!in_array($athlete->club_id, $userClubs->toArray())) {
            abort(403);
        }

        // Załaduj wyniki zawodów
        $results = CompetitionResult::where('athlete_id', $athlete->id)
            ->with('competition')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Athletes/Results', [
            'athlete' => $athlete,
            'results' => $results,
        ]);
    }

    public function showByCompetition(Competition $competition)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy zawody należą do klubu użytkownika
        if (!in_array($competition->club_id, $userClubs->toArray())) {
            abort(403);
        }

        // Załaduj wyniki zawodów
        $results = CompetitionResult::where('competition_id', $competition->id)
            ->with('athlete')
            ->orderBy('rank')
            ->paginate(15);

        return Inertia::render('Competitions/Results', [
            'competition' => $competition,
            'results' => $results,
        ]);
    }
}
