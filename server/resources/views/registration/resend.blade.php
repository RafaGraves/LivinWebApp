<!DOCTYPE html>
<html>
<head>
    <title>Email Resent | My Website</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            padding: 50px 20px;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 50px;
        }

        p {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        a {
            color: rgba(0, 0, 0, 0);
            text-decoration: none;
            font-weight: bold;
        }

        button {
            background-color: #e00000;
            color: #000;
            border: none;
            padding: 15px 30px;
            font-size: 1.5rem;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>{{ $fn }} {{ $ln }}. Correo de confirmación reenviado </h1>
    <p>Tu correo de confirmación se ha reenviado. {{ $fn }}, Revisa tu buzón de entrada o tu carpeta de spam.</p>
    <button><a href="{{ $url }}/index.html">Back to Home</a></button>
</div>
</body>
</html>
