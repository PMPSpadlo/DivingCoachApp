<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Club;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AthleteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Pobieramy kluby, do których należy zalogowany użytkownik
        $userClubs = auth()->user()->clubs->pluck('id');

        // Filtrowanie po klubie, jeśli parametr został przekazany
        $clubId = $request->input('club_id');
        $query = Athlete::query();

        if ($clubId && in_array($clubId, $userClubs->toArray())) {
            $query->where('club_id', $clubId);
            $selectedClub = Club::find($clubId);
        } else {
            $query->whereIn('club_id', $userClubs);
            $selectedClub = null;
        }

        // Filtrowanie po kategorii wiekowej
        if ($request->has('age_category') && in_array($request->age_category, ['junior', 'senior'])) {
            $query->where('age_category', $request->age_category);
        }

        // Filtrowanie po płci
        if ($request->has('gender') && in_array($request->gender, ['male', 'female', 'other'])) {
            $query->where('gender', $request->gender);
        }

        // Sortowanie (domyślnie po nazwisku)
        $query->orderBy('last_name')->orderBy('first_name');

        $athletes = $query->with('club')->get();
        $clubs = auth()->user()->clubs;

        return Inertia::render('Athletes/Index', [
            'athletes' => $athletes,
            'clubs' => $clubs,
            'filters' => [
                'club_id' => $clubId,
                'age_category' => $request->input('age_category'),
                'gender' => $request->input('gender'),
            ],
            'selectedClub' => $selectedClub,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clubs = auth()->user()->clubs;
        $preselectedClubId = $request->input('club_id');

        // Sprawdzamy czy preselectedClubId należy do klubów użytkownika
        if ($preselectedClubId && !$clubs->contains('id', $preselectedClubId)) {
            abort(403);
        }

        return Inertia::render('Athletes/Create', [
            'clubs' => $clubs,
            'preselectedClubId' => $preselectedClubId,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'club_id' => 'required|exists:clubs,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'age_category' => 'required|in:junior,senior',
        ]);

        // Sprawdź czy użytkownik ma dostęp do wybranego klubu
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($validated['club_id'], $userClubs)) {
            abort(403);
        }

        $athlete = Athlete::create($validated);

        return redirect()->route('athletes.show', $athlete)
            ->with('success', 'Zawodnik został dodany pomyślnie.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Athlete $athlete)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu zawodnika
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($athlete->club_id, $userClubs)) {
            abort(403);
        }

        // Załaduj relacje z odpowiednimi ograniczeniami, dodając relację competitions
        $athlete->load([
            'club',
            'trainings' => function ($query) {
                $query->orderBy('date', 'desc')
                    ->limit(5)
                    ->select('trainings.id', 'trainings.date', 'trainings.location', 'trainings.type', 'trainings.notes');
            },
            'competitions' => function ($query) {
                $query->orderBy('date', 'desc')
                    ->limit(5);
            },
            'competitionResults' => function ($query) {
                $query->orderBy('created_at', 'desc')
                    ->limit(5);
            },
            'competitionResults.competition', // Proste ładowanie relacji competition
            'payments' => function ($query) {
                $query->orderBy('payment_date', 'desc')
                    ->limit(5)
                    ->select('id', 'athlete_id', 'amount', 'payment_date', 'status', 'currency');
            },
        ]);

        // Dodaj debugowanie, aby sprawdzić dane
        \Log::info('Athlete data for ID ' . $athlete->id, [
            'competitionResults_count' => $athlete->competitionResults->count(),
            'competitions_count' => $athlete->competitions->count(),
            'trainings_count' => $athlete->trainings->count(),
            'payments_count' => $athlete->payments->count(),
        ]);

        $competitionResults = \App\Models\CompetitionResult::where('athlete_id', $athlete->id)
            ->with('competition')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Athletes/Show', [
            'athlete' => $athlete,
            'directResults' => $competitionResults
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Athlete $athlete)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu zawodnika
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($athlete->club_id, $userClubs)) {
            abort(403);
        }

        return Inertia::render('Athletes/Edit', [
            'athlete' => $athlete,
            'clubs' => auth()->user()->clubs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Athlete $athlete)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu zawodnika
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($athlete->club_id, $userClubs)) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'club_id' => 'required|exists:clubs,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'age_category' => 'required|in:junior,senior',
        ]);

        // Sprawdź czy nowy klub należy do użytkownika
        if (!in_array($validated['club_id'], $userClubs)) {
            abort(403);
        }

        $athlete->update($validated);

        return redirect()->route('athletes.show', $athlete)
            ->with('success', 'Dane zawodnika zostały zaktualizowane.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Athlete $athlete)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu zawodnika
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($athlete->club_id, $userClubs)) {
            abort(403);
        }

        $athlete->delete();

        return redirect()->route('athletes.index')
            ->with('success', 'Zawodnik został usunięty.');
    }
}
