<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro confirmado</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f2f2f2;
        }

        .confirmation {
            text-align: center;
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        }

        h1 {
            margin-top: 0;
            font-size: 36px;
        }

        p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        button {
            background-color: #3f51b5;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2c3e50;
        }
    </style>
</head>
<body>
<div class="confirmation">
    <h1>{{ $name }} {{ $lastName }}<br>Registro confirmado</h1>
    <p>Gracias por registrarte en Livin. Tu registro ha sido confirmado</p>
    <button onclick="window.location.href = '{{ $url }}/index.html';">Regresar a inicio</button>
</div>
</body>
</html>
