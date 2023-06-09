<!DOCTYPE html>
<html>
<head>
    <title>Gracias por registrarte en living</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 36px;
            margin-top: 0;
        }

        p {
            font-size: 18px;
            line-height: 1.5;
        }

        .p0{
            font-size: 24px;
        }

        .p1{
            font-size: 20px;
        }

        .name {
            font-weight: bold;
            color: #007ecc;
        }

        .confirmation-link {
            display: inline-block;
            background-color: #0052cc;
            color: #fff;
            font-size: 18px;
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }

        .confirmation-link:hover {
            background-color: #003d99;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>¡Gracias por registrarte con nosotros</h1>
    <p class="p0">Estimado <span class="name">{{ $confirmationData['name'] }} {{ $confirmationData['lastname'] }}</span>,</p>
    <p class="p1">¡Hemos recibido tu registro y nos complace que te unas a la comunidad de Livin! Revisa tu correo electrónico porque has recibido un mensaje para que confirmes tu registro y podamos activar tu cuenta:</p>
    <a href="{{ $confirmationData['link'] }}" class="confirmation-link">Confirma tu registro</a>
    <p>Si tienes preguntas o necesitas asistencia, por favor, no dudes en contactarnos al correo eléctronico <a href="mailto:info@example.com">info@example.com</a>.</p>
    <p>Saludos</p>
    <p>El equipo de Livin</p>
</div>
</body>
</html>
