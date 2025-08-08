<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Event;
use App\Models\Certificate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

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

    // app/Http/Controllers/CertificateController.php

    public function generate(Request $request, $eventId, $userId)
    {
        $user = User::findOrFail($userId);
        $event = Event::findOrFail($eventId);
        $type = $request->input('tipo', 'Por su destacada participacion en el ecvento');

        // Convertir la fecha string a Carbon si es necesario
        $eventDate = \Carbon\Carbon::parse($event->fecha_fin);

        // Ruta al template PDF escaneado
        $templatePath = public_path('templates/Template.pdf');
        
        $pdf = new Fpdi();
        $pdf->AddPage();
        $pageCount = $pdf->setSourceFile($templatePath);
        $templateId = $pdf->importPage(1);
        $pdf->useTemplate($templateId);
        
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        
        // Escribir nombre del usuario en color gris
        $pdf->SetTextColor(100, 100, 100); // RGB para gris medio
        $pdf->SetXY(20, 118);
        $pdf->Cell(0, 10, $user->name, 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0); // Restaurar a negro para el resto del texto
        
        // Escribir tipo de certificado
        $pdf->SetXY(20, 128);
        $pdf->Cell(0, 10, 'POR SU DESTACADA PARTICIPACION EN EL EVENTO', 0, 1, 'C');

        // Escribir nombre del evento
        $pdf->SetTextColor(100, 100, 100); // RGB para gris medio
        $pdf->SetXY(20, 138);
        $pdf->Cell(0, 10, $event->nombre, 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0); // Restaurar a negro para el resto del texto

        // Escribir tipo de certificado
        $pdf->SetTextColor(100, 100, 100); // RGB para gris medio
        $pdf->SetXY(20, 148);
        $pdf->Cell(0, 10, ' UN REFLEJO DEL COMPROMISO INSTITUCIONAL CON EL', 0, 1, 'C');
        $pdf->SetXY(20, 152);
        $pdf->Cell(0, 10, 'FORTALECIMIENTO DE LA INVESTIGACION, EL DESARROLLO ', 0, 1, 'C');
        $pdf->SetXY(20, 156);
        $pdf->Cell(0, 10, 'ACADEMICO Y LA FORMACION DE TALENTO CIENTIFICO EN NUESTRO PAIS..', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0); // Restaurar a negro para el resto del texto



        
        // Escribir fecha (usando el objeto Carbon)
        $pdf->SetXY(20, 200);
        $pdf->Cell(0, 10, $eventDate->format('d/m/Y'), 0, 1, 'C');
        
            // Nombre del archivo
        $filename = "{$eventId}_{$userId}.pdf";
        $directory = 'certificados';
        
        // Guardar en storage
        Storage::disk('public')->makeDirectory($directory);
        $pdfPath = "{$directory}/{$filename}";
        $pdfContent = $pdf->Output('S');
        
        Storage::disk('public')->put($pdfPath, $pdfContent);
        
        // Descargar el archivo
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function generateBatch(Request $request, $eventId)
    {
        $userIds = $request->input('user_ids', []);
        $type = $request->input('tipo', 'Por su destacada participación en el evento');
        $zipFileName = "certificados_evento_{$eventId}.zip";
        $zipPath = storage_path("app/public/{$zipFileName}");
        
        // Crear archivo ZIP
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($userIds as $userId) {
                // Generar cada certificado
                $certificateContent = $this->generateCertificateForUser($eventId, $userId, $type);
                
                // Nombre del archivo en el ZIP
                $filenameInZip = "{$eventId}_{$userId}.pdf";
                $zip->addFromString($filenameInZip, $certificateContent);
            }
            $zip->close();
        }
        
        // Crear enlace simbólico si no existe
        if (!Storage::disk('public')->exists($zipFileName)) {
            return response()->json(['error' => 'Error al generar el ZIP'], 500);
        }
        
        // URL pública para descargar el ZIP
        $publicUrl = Storage::disk('public')->url($zipFileName);
        
        return response()->json([
            'message' => 'Certificados generados correctamente',
            'count' => count($userIds),
            'download_url' => $publicUrl
        ]);
    }

    // Función auxiliar para generar certificados individuales
    private function generateCertificateForUser($eventId, $userId, $type)
    {
        $user = User::findOrFail($userId);
        $event = Event::findOrFail($eventId);
        $eventDate = \Carbon\Carbon::parse($event->fecha_fin);

        $pdf = new Fpdi();
        $pdf->AddPage();
        $pdf->setSourceFile(public_path('templates/Template.pdf'));
        $templateId = $pdf->importPage(1);
        $pdf->useTemplate($templateId);
        
        $pdf->SetFont('Arial', 'B', 14);
        
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(20, 118);
        $pdf->Cell(0, 10, $user->name, 0, 1, 'C');
        
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(20, 128);
        $pdf->Cell(0, 10, 'POR SU DESTACADA PARTICIPACION EN EL EVENTO', 0, 1, 'C');

        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(20, 138);
        $pdf->Cell(0, 10, $event->nombre, 0, 1, 'C');
        
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(50, 160);
        $pdf->Cell(0, 10, $eventDate->format('d/m/Y'), 0, 1, 'C');
        
        return $pdf->Output('S');
    }
}

