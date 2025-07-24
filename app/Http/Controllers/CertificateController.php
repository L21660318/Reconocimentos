<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Event;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
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
            // Guardar o actualizar certificado
            $certificate = Certificate::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $cert['user_id']],
                ['tipo' => $cert['tipo']]
            );

            // Obtener usuario
            $user = User::with('knowledgeArea')->findOrFail($cert['user_id']);

            // Generar PDF
            $pdf = Pdf::loadView('pdf.certificate', [
                'event' => $event,
                'user' => $user,
                'certificate' => $certificate,
            ]);

            // Guardar el PDF en el disco
            $filename = "certificados/{$event->id}_{$user->id}.pdf";
            \Storage::put("public/$filename", $pdf->output());

            // (Opcional) Guardar ruta del PDF en la base de datos
            $certificate->update([
                'file_path' => $filename,
            ]);
        }

        return redirect()->back()->with('success', 'Certificados generados correctamente.');
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

    // Cambia esto en CertificateController
    public function preview(Event $event, User $user, Request $request)
    {
        $tipo = $request->get('tipo', 'Participación');

        $certificate = new \stdClass(); // si necesitas un objeto
        $certificate->tipo = $tipo;

        $pdf = Pdf::loadView('pdf.certificate', [  // usa la misma vista
            'event' => $event,
            'user' => $user,
            'certificate' => $certificate, // para compatibilidad con la plantilla
        ]);

        return $pdf->stream("preview.pdf");
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
