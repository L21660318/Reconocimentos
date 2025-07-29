<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 30px;
        }
        .header-image {
            max-width: 100%;
            height: auto;
            margin: 5px 0;
        }
        .separator {
            border-top: 1px solid #000;
            margin: 15px auto;
            width: 80%;
        }
        .content {
            font-size: 18px;
            margin: 15px 0;
            line-height: 1.5;
        }
        .participante {
            font-size: 20px;
            font-weight: bold;
            margin: 30px 0;
        }
        .footer {
            margin-top: 40px;
            font-size: 14px;
        }
        .logo-section {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="certificado">
        <!-- Encabezado institucional con imágenes -->
        <div class="header">
            <!-- Imágenes en base64 para evitar problemas de rutas en PDF -->
            <img src="@php echo base64Image(public_path('images/secretaria.jpg')); @endphp" alt="Secretaría de Educación Pública" style="height: 40px;">
            
            <img src="@php echo base64Image(public_path('images/tec.jpg')); @endphp" alt="TECNOLOGICO NACIONAL DE MEXICO" style="height: 50px; margin: 10px 0;">

            <div class="separator"></div>
            
            <div class="logo-section">
                <img src="@php echo base64Image(public_path('images/cenidet.jpg')); @endphp" alt="cenidet" style="height: 60px;">
            </div>
        </div>

        <!-- Resto del contenido (igual que antes) -->
        <div class="content">
            <p>Por su destacada participación en el</p>
            <p><strong>{{ $event->nombre }}</strong></p>

            <p>
                logrando la selección de <strong>{{ $event->users->count() }} estudiantes</strong>, un reflejo del
                compromiso institucional con el fortalecimiento de la investigación,
            </p>
            <p>
                el desarrollo académico y la formación de talento científico en nuestro país.
            </p>
        </div>

        <p class="participante">{{ $user->name }}</p>

        <p class="footer">
            {{ $state->name ?? 'Estado desconocido' }}, 
            {{ $country->name ?? 'País desconocido' }}, 
            {{ \Carbon\Carbon::parse($event->fecha_fin)->format('d') }} de 
            {{ \Carbon\Carbon::parse($event->fecha_fin)->translatedFormat('F') }} de 
            {{ \Carbon\Carbon::parse($event->fecha_fin)->format('Y') }}
        </p>

        <p class="footer">Fecha de emisión: {{ now()->format('d/m/Y') }}</p>
        <p class="footer">Folio: {{ $certificate->folio ?? '---------' }}</p>
        <p class="footer">http://constancias.cenidet.tecnm.mx</p>

        <p class="footer" style="margin-top: 20px;">
            <strong>Sello Digital:</strong><br>
            {!! nl2br(e(wordwrap($certificate->sello_digital ?? '---------------', 64))) !!}
        </p>
    </div>
</body>
</html>