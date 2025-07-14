<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventReviewController extends Controller
{
    public function index()
    {
        // Solo eventos sin revisión
        $events = Event::whereDoesntHave('revisiones')->get();

        return Inertia::render('Catalogs/Reviewer/EventReviewIndex', [
            'events' => $events,
            'title' => 'Eventos para revisión',
        ]);
    }

    public function edit(Event $event)
    {
        $pivot = $event->revisiones()->where('reviewer_id', auth()->id())->first()?->pivot;

        return Inertia::render('Catalogs/Reviewer/EventReviewEdit', [
            'event' => $event,
            'pivot' => $pivot,
            'title' => 'Revisión de evento',
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'estatus' => 'required|in:aceptado,rechazado',
            'comentario' => 'nullable|string',
        ]);
    
        // Prevenir doble revisión
        if ($event->revisiones()->exists()) {
            return redirect()->route('event-review.index')->with('error', 'Este evento ya fue revisado.');
        }
    
        $event->revisiones()->attach(auth()->id(), [
            'estatus' => $request->estatus,
            'comentario' => $request->comentario,
            'tipo' => 'inicial',
        ]);
    
        return redirect()->route('event-review.index')->with('success', 'Revisión enviada correctamente.');
    }
    
}
