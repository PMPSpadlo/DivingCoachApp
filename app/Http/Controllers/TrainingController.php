<?php

// TrainingController.php
namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Athlete;
use App\Models\Club;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $userClubs = auth()->user()->clubs->pluck('id');
        $clubId = $request->input('club_id');
        $query = Training::query();

        if ($clubId && in_array($clubId, $userClubs->toArray())) {
            $query->where('club_id', $clubId);
            $selectedClub = Club::find($clubId);
        } else {
            $query->whereIn('club_id', $userClubs);
            $selectedClub = null;
        }

        // Filtrowanie po typie treningu
        if ($request->has('type') && in_array($request->type, ['general', 'technical', 'strength', 'conditioning'])) {
            $query->where('type', $request->type);
        }

        // Filtrowanie po dacie
        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Sortowanie (domyślnie po dacie malejąco)
        $query->orderBy('date', 'desc');

        $trainings = $query->with(['club', 'trainer'])->paginate(10);
        $clubs = auth()->user()->clubs;

        return Inertia::render('Trainings/Index', [
            'trainings' => $trainings,
            'clubs' => $clubs,
            'filters' => [
                'club_id' => $clubId,
                'type' => $request->input('type'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
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

        // Pobierz zawodników z wybranego klubu
        $athletes = $preselectedClubId
            ? Athlete::where('club_id', $preselectedClubId)->orderBy('last_name')->get()
            : collect();

        return Inertia::render('Trainings/Create', [
            'clubs' => $clubs,
            'preselectedClubId' => $preselectedClubId,
            'athletes' => $athletes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'type' => 'required|in:general,technical,strength,conditioning',
            'notes' => 'nullable|string',
            'athletes' => 'required|array|min:1',
            'athletes.*' => 'exists:athletes,id',
        ]);

        // Sprawdź czy użytkownik ma dostęp do wybranego klubu
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($validated['club_id'], $userClubs)) {
            abort(403);
        }

        // Sprawdź czy zawodnicy należą do wybranego klubu
        foreach ($validated['athletes'] as $athleteId) {
            $athlete = Athlete::find($athleteId);
            if (!$athlete || $athlete->club_id != $validated['club_id']) {
                abort(403, 'One of the selected athletes does not belong to the selected club.');
            }
        }

        // Utwórz trening
        $training = Training::create([
            'club_id' => $validated['club_id'],
            'trainer_id' => auth()->id(),
            'date' => $validated['date'],
            'location' => $validated['location'],
            'type' => $validated['type'],
            'notes' => $validated['notes'],
        ]);

        // Przypisz zawodników do treningu
        $training->athletes()->attach($validated['athletes']);

        return redirect()->route('trainings.show', $training)
            ->with('success', 'Trening został dodany pomyślnie.');
    }

    public function show(Training $training)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu treningu
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($training->club_id, $userClubs)) {
            abort(403);
        }

        // Załaduj relacje
        $training->load(['club', 'trainer', 'athletes']);

        return Inertia::render('Trainings/Show', [
            'training' => $training,
        ]);
    }

    public function edit(Training $training)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu treningu
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($training->club_id, $userClubs)) {
            abort(403);
        }

        // Pobierz zawodników z klubu treningu
        $athletes = Athlete::where('club_id', $training->club_id)->orderBy('last_name')->get();
        $selectedAthletes = $training->athletes->pluck('id')->toArray();

        return Inertia::render('Trainings/Edit', [
            'training' => $training,
            'club' => $training->club,
            'athletes' => $athletes,
            'selectedAthletes' => $selectedAthletes,
        ]);
    }

    public function update(Request $request, Training $training)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu treningu
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($training->club_id, $userClubs)) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'type' => 'required|in:general,technical,strength,conditioning',
            'notes' => 'nullable|string',
            'athletes' => 'required|array|min:1',
            'athletes.*' => 'exists:athletes,id',
        ]);

        // Sprawdź czy zawodnicy należą do klubu treningu
        foreach ($validated['athletes'] as $athleteId) {
            $athlete = Athlete::find($athleteId);
            if (!$athlete || $athlete->club_id != $training->club_id) {
                abort(403, 'One of the selected athletes does not belong to the training club.');
            }
        }

        // Aktualizuj trening
        $training->update([
            'date' => $validated['date'],
            'location' => $validated['location'],
            'type' => $validated['type'],
            'notes' => $validated['notes'],
        ]);

        // Aktualizuj zawodników treningu
        $training->athletes()->sync($validated['athletes']);

        return redirect()->route('trainings.show', $training)
            ->with('success', 'Trening został zaktualizowany pomyślnie.');
    }

    public function destroy(Training $training)
    {
        // Sprawdź czy użytkownik ma dostęp do klubu treningu
        $userClubs = auth()->user()->clubs->pluck('id')->toArray();
        if (!in_array($training->club_id, $userClubs)) {
            abort(403);
        }

        $training->delete();

        return redirect()->route('trainings.index')
            ->with('success', 'Trening został usunięty.');
    }
}
