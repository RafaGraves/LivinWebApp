<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link Expired | Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #252525;
            color: #fff;
            margin: 0;
        }

        header {
            background-color: #000;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
        }

        nav li {
            margin: 0 10px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }

        nav a:hover {
            color: #00c8ff;
        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .container {
            background-color: #333;
            padding: 50px;
            text-align: center;
            box-shadow: 0 0 20px #000;
        }

        h1 {
            margin: 0;
            font-size: 36px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        form button {
            background-color: #00c8ff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        form button:hover {
            background-color: #007cad;
        }
    </style>
</head>
<body>
<header>
    <h1>Link expirado</h1>
</header>
<main>
    <div class="container">
        <h2>{{ $fn }} {{ $ln }}. El link ha expirado</h2>
        <p>El link que intentas accesar ha expirado. Da click aquí para reenviar el link</p>
        <form method="post" action="{{ $resendUrl }}">
            <input type="hidden" name="extra_submit_param" value="extra_submit_value"/>
            <button type="submit">Reenviar confirmación</button>
        </form>
    </div>
</main>
</body>
</html>
