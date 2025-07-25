<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .certificado {
            border: 5px double #444;
            padding: 50px;
            margin: auto;
            max-width: 800px;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        p {
            font-size: 18px;
            margin: 12px 0;
        }
        .footer {
            margin-top: 40px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="certificado">
        <!-- Nombre de la institución -->
        <h1>{{ $institution->name ?? 'INSTITUCIÓN DESCONOCIDA' }}</h1>

        <!-- Contenido del certificado -->
        <p>Por su destacada participación en el</p>
        <p><strong>{{ $event->nombre }}</strong></p>

        <p>
            logrando la selección de <strong>{{ $event->users->count() }} estudiantes</strong>, un reflejo del
            compromiso institucional con el fortalecimiento de la investigación,
        </p>
        <p>
            el desarrollo académico y la formación de talento científico en nuestro país.
        </p>

        <!-- Nombre del alumno -->
        <p><strong>{{ $user->name }}</strong></p>

        <!-- Lugar y fecha del evento -->
        <p class="footer">
            {{ $state->name ?? 'Estado desconocido' }}, 
            {{ $country->name ?? 'País desconocido' }}, 
            {{ \Carbon\Carbon::parse($event->fecha_fin)->format('d') }} de 
            {{ \Carbon\Carbon::parse($event->fecha_fin)->translatedFormat('F') }} de 
            {{ \Carbon\Carbon::parse($event->fecha_fin)->format('Y') }}
        </p>

        <!-- Fecha de emisión y folio -->
        <p class="footer">Fecha de emisión: {{ now()->format('d/m/Y') }}</p>
        <p class="footer">Folio: {{ $certificate->folio ?? '---------' }}</p>
        <p class="footer">http://constancias.cenidet.tecnm.mx</p>

        <!-- Sello digital -->
        <p class="footer" style="margin-top: 20px;">
            <strong>Sello Digital:</strong><br>
            {!! nl2br(e(wordwrap($certificate->sello_digital ?? '---------------', 64))) !!}
        </p>
    </div>
</body>
</html>
--