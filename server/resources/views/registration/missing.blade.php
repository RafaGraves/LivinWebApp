<!DOCTYPE html>
<html>
<head>
    <title>Link Not Found</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 50px;
            background-color: #fff;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            margin-top: 50px;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 30px;
        }

        p {
            font-size: 20px;
            margin-bottom: 30px;
        }

        button {
            padding: 15px 30px;
            font-size: 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0062cc;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Oops!</h1>
    <p>El link no existe</p>
    <button onclick="window.location.href = '{{ $url }}/index.html';">Regresar a inicio</button>
</div>
</body>
</html>
