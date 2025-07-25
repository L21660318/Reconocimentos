<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Event;
use App\Models\Certificate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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
        $event->load([
            'users.knowledgeArea',
            'users.institution.state',
            'users.institution.country'
        ]);

        return Inertia::render('Catalogs/Certificate/Show', [
            'event' => $event,
            'users' => $event->users,
            'title' => "Usuarios del evento: $event->nombre",
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $event->load('users'); // 👈 Cargar usuarios para usar count() en la vista

        $request->validate([
            'certificados' => 'required|array',
            'certificados.*.user_id' => 'required|exists:users,id',
            'certificados.*.tipo' => 'required|string|max:255',
        ]);

        foreach ($request->certificados as $cert) {
            $certificate = Certificate::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $cert['user_id']],
                ['tipo' => $cert['tipo']]
            );

            $user = User::with([
                'knowledgeArea',
                'institution.state',
                'institution.country'
            ])->findOrFail($cert['user_id']);

            $pdf = Pdf::loadView('pdf.certificate', [
                'event' => $event,
                'user' => $user,
                'certificate' => $certificate,
                'institution' => $user->institution,
                'state' => optional($user->institution)->state,
                'country' => optional($user->institution)->country,
            ]);

            $filename = "certificados/{$event->id}_{$user->id}.pdf";
            Storage::put("public/$filename", $pdf->output());

            $certificate->update([
                'file_path' => $filename,
            ]);
        }

        return redirect()->back()->with('success', 'Certificados generados correctamente.');
    }

    public function preview(Event $event, User $user, Request $request)
    {
        $event->load('users'); // 👈 También cargar usuarios aquí

        $tipo = $request->get('tipo', 'Participación');

        $certificate = new \stdClass();
        $certificate->tipo = $tipo;

        $user->load([
            'knowledgeArea',
            'institution.state',
            'institution.country'
        ]);

        $pdf = Pdf::loadView('pdf.certificate', [
            'event' => $event,
            'user' => $user,
            'certificate' => $certificate,
            'institution' => $user->institution,
            'state' => optional($user->institution)->state,
            'country' => optional($user->institution)->country,
        ]);

        return $pdf->stream("preview.pdf");
    }

    public function download(Event $event, User $user)
    {
        $filePath = storage_path("app/public/certificados/{$event->id}_{$user->id}.pdf");

        if (!file_exists($filePath)) {
            abort(404, 'Certificado no encontrado. Asegúrate de haberlo generado.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=certificado_{$user->name}_{$event->nombre}.pdf"
        ]);
    }

    public function myCertificates()
    {
        $user = auth()->user();

        $certificates = Certificate::with('event')
            ->where('user_id', $user->id)
            ->get();

        return Inertia::render('Certificates/MyCertificates', [
            'certificates' => $certificates,
            'title' => 'Mis Certificados',
        ]);
    }
}
