<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificado</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 100px; }
        h1 { font-size: 28px; margin-bottom: 20px; }
        .content { font-size: 20px; }
    </style>
</head>
<body>
    <h1>Certificado de {{ $tipo }}</h1>
    <p class="content">
        Se otorga el presente certificado a <strong>{{ $user->name }}</strong>,<br>
        perteneciente al área de conocimiento <strong>{{ $user->knowledgeArea->name ?? 'No asignada' }}</strong>,<br>
        por su participación en el evento <strong>{{ $event->nombre }}</strong>.
    </p>
</body>
</html>
