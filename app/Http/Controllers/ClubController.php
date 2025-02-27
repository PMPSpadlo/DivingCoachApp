<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clubs = auth()->user()->clubs;

        return Inertia::render('Clubs/Index', [
            'clubs' => $clubs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Clubs/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:clubs',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255|default:Poland',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
        ]);

        $club = Club::create([
            ...$validated,
            'owner_id' => auth()->id(),
            'active' => true,
        ]);

        // Przypisz zalogowanego użytkownika jako właściciela klubu
        $club->users()->attach(auth()->id(), ['role' => 'club_owner', 'is_active' => true]);

        return redirect()->route('clubs.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Club $club)
    {
        // Sprawdź czy użytkownik ma dostęp do tego klubu
        if (!auth()->user()->clubs->contains($club)) {
            abort(403);
        }

        // Załaduj relacje
        $club->load([
            'athletes' => fn($query) => $query->limit(5),
            'trainings' => fn($query) => $query->orderBy('date', 'desc')->limit(5),
            'competitions' => fn($query) => $query->orderBy('date', 'desc')->limit(5),
            'owner'
        ]);

        return Inertia::render('Clubs/Show', [
            'club' => $club,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Club $club)
    {
        // Sprawdź czy użytkownik ma dostęp do tego klubu
        if (!auth()->user()->clubs->contains($club)) {
            abort(403);
        }

        return Inertia::render('Clubs/Edit', [
            'club' => $club,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Club $club)
    {
        // Sprawdź czy użytkownik ma dostęp do tego klubu
        if (!auth()->user()->clubs->contains($club)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:clubs,name,'.$club->id,
            'city' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'active' => 'boolean',
        ]);

        $club->update($validated);

        return redirect()->route('clubs.show', $club);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Club $club)
    {
        // Sprawdź czy użytkownik jest właścicielem klubu
        if ($club->owner_id !== auth()->id()) {
            abort(403);
        }

        $club->delete();

        return redirect()->route('clubs.index');
    }
}
