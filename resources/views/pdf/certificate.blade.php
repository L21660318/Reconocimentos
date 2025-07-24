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
        }
        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }
        p {
            font-size: 20px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="certificado">
        <h1>{{ $certificate->tipo }}</h1>
        <p>Se otorga a</p>
        <p><strong>{{ $user->name }}</strong></p>
        <p>por su participación en el evento</p>
        <p><strong>{{ $event->nombre }}</strong></p>
        <p>perteneciente al área de <strong>{{ $user->knowledgeArea->name ?? 'Sin área' }}</strong></p>
        <p>Fecha de emisión: {{ now()->format('d/m/Y') }}</p>
    </div>
</body>
</html>
