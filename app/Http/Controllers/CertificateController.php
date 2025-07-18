<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Event;
use App\Models\Certificate;


class CertificateController extends Controller
{
    public function index()
    {
        return Inertia::render('Catalogs/Certificate/Index', [
            'events' => Event::withCount('users')->get(),
            'title' => 'Generar certificados',
        ]);
    }

    public function show(Event $event)
    {
        return Inertia::render('Catalogs/Certificate/Show', [
            'event' => $event,
            'users' => $event->users, // Aquí ya están cargados con knowledgeArea
            'title' => "Usuarios del evento: $event->nombre",
        ]);

    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'certificados' => 'required|array',
            'certificados.*.user_id' => 'required|exists:users,id',
            'certificados.*.tipo' => 'required|string|max:255',
        ]);

        foreach ($request->certificados as $cert) {
            Certificate::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $cert['user_id']],
                ['tipo' => $cert['tipo']]
            );
        }

        return redirect()->back()->with('success', 'Certificados generados correctamente.');
    }

}
