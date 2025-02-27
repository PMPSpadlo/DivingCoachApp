<?php

// PaymentController.php
namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Club;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $userClubs = auth()->user()->clubs->pluck('id');
        $clubId = $request->input('club_id');
        $query = Payment::query();

        if ($clubId && in_array($clubId, $userClubs->toArray())) {
            $query->where('club_id', $clubId);
            $selectedClub = Club::find($clubId);
        } else {
            $query->whereIn('club_id', $userClubs);
            $selectedClub = null;
        }

        // Filtrowanie po zawodniku
        if ($request->has('athlete_id')) {
            $query->where('athlete_id', $request->athlete_id);
        }

        // Filtrowanie po statusie
        if ($request->has('status') && in_array($request->status, ['pending', 'paid', 'overdue'])) {
            $query->where('status', $request->status);
        }

        // Filtrowanie po metodzie płatności
        if ($request->has('payment_method') && in_array($request->payment_method, ['cash', 'bank_transfer', 'card', 'paypal'])) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtrowanie po dacie
        if ($request->has('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        // Sortowanie (domyślnie po dacie malejąco)
        $query->orderBy('payment_date', 'desc');

        $payments = $query->with(['club', 'athlete'])->paginate(15);
        $clubs = auth()->user()->clubs;

        // Pobierz wszystkich zawodników z klubów użytkownika dla celów filtrowania
        $athletes = Athlete::whereIn('club_id', $userClubs)->orderBy('last_name')->get();

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'clubs' => $clubs,
            'athletes' => $athletes,
            'filters' => [
                'club_id' => $clubId,
                'athlete_id' => $request->input('athlete_id'),
                'status' => $request->input('status'),
                'payment_method' => $request->input('payment_method'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ],
            'selectedClub' => $selectedClub,
        ]);
    }

    public function create(Request $request)
    {
        $userClubs = auth()->user()->clubs->pluck('id');
        $clubId = $request->input('club_id');
        $athleteId = $request->input('athlete_id');

        // Sprawdź czy clubId należy do klubów użytkownika
        $club = null;
        $clubAthletes = collect();

        if ($clubId) {
            $club = Club::whereIn('id', $userClubs)->find($clubId);
            if (!$club) {
                abort(403);
            }

            // Pobierz zawodników z wybranego klubu
            $clubAthletes = Athlete::where('club_id', $clubId)->orderBy('last_name')->get();
        }

        // Jeśli podano athlete_id, sprawdź czy zawodnik należy do klubów użytkownika
        $athlete = null;
        if ($athleteId) {
            $athlete = Athlete::whereIn('club_id', $userClubs)->find($athleteId);
            if (!$athlete) {
                abort(403);
            }
            // Jeśli klub nie został określony, ustaw go na klub zawodnika
            if (!$club) {
                $club = $athlete->club;
                $clubAthletes = Athlete::where('club_id', $club->id)->orderBy('last_name')->get();
            }
        }

        return Inertia::render('Payments/Create', [
            'clubs' => auth()->user()->clubs,
            'selectedClub' => $club,
            'clubAthletes' => $clubAthletes,
            'selectedAthlete' => $athlete,
        ]);
    }

    public function store(Request $request)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'athlete_id' => 'required|exists:athletes,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'currency' => 'required|in:PLN,EUR,USD',
            'status' => 'required|in:pending,paid,overdue',
            'payment_method' => 'required|in:cash,bank_transfer,card,paypal',
        ]);

        // Sprawdź czy klub należy do użytkownika
        if (!in_array($validated['club_id'], $userClubs->toArray())) {
            abort(403);
        }

        // Sprawdź czy zawodnik należy do wybranego klubu
        $athlete = Athlete::find($validated['athlete_id']);
        if (!$athlete || $athlete->club_id != $validated['club_id']) {
            abort(403, 'Wybrany zawodnik nie należy do wybranego klubu.');
        }

        $payment = Payment::create($validated);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Płatność została dodana pomyślnie.');
    }

    public function show(Payment $payment)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy płatność należy do klubu użytkownika
        if (!in_array($payment->club_id, $userClubs->toArray())) {
            abort(403);
        }

        // Załaduj relacje
        $payment->load(['club', 'athlete']);

        return Inertia::render('Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function edit(Payment $payment)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy płatność należy do klubu użytkownika
        if (!in_array($payment->club_id, $userClubs->toArray())) {
            abort(403);
        }

        // Załaduj relacje
        $payment->load(['club', 'athlete']);

        // Pobierz zawodników z klubu płatności
        $clubAthletes = Athlete::where('club_id', $payment->club_id)->orderBy('last_name')->get();

        return Inertia::render('Payments/Edit', [
            'payment' => $payment,
            'clubAthletes' => $clubAthletes,
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy płatność należy do klubu użytkownika
        if (!in_array($payment->club_id, $userClubs->toArray())) {
            abort(403);
        }

        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string|max:255',
            'currency' => 'required|in:PLN,EUR,USD',
            'status' => 'required|in:pending,paid,overdue',
            'payment_method' => 'required|in:cash,bank_transfer,card,paypal',
        ]);

        // Sprawdź czy zawodnik należy do klubu płatności
        $athlete = Athlete::find($validated['athlete_id']);
        if (!$athlete || $athlete->club_id != $payment->club_id) {
            abort(403, 'Wybrany zawodnik nie należy do klubu płatności.');
        }

        $payment->update($validated);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Płatność została zaktualizowana pomyślnie.');
    }

    public function destroy(Payment $payment)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy płatność należy do klubu użytkownika
        if (!in_array($payment->club_id, $userClubs->toArray())) {
            abort(403);
        }

        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Płatność została usunięta.');
    }

    // Dodatkowa metoda do wyświetlania płatności zawodnika
    public function showByAthlete(Athlete $athlete)
    {
        $userClubs = auth()->user()->clubs->pluck('id');

        // Sprawdź czy zawodnik należy do klubu użytkownika
        if (!in_array($athlete->club_id, $userClubs->toArray())) {
            abort(403);
        }

        // Załaduj płatności
        $payments = Payment::where('athlete_id', $athlete->id)
            ->orderBy('payment_date', 'desc')
            ->paginate(15);

        return Inertia::render('Athletes/Payments', [
            'athlete' => $athlete,
            'payments' => $payments,
        ]);
    }
}
