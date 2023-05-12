<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Operation Could Not Be Completed</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 50px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }

        button {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Operation Could Not Be Completed</h1>
    <p>Unfortunately, the requested operation could not be completed at this time. Please try again later or contact our
        support team for assistance.</p>
    <button onclick="window.location.href = '{{ $url }}/index.html';">Retry</button>
</div>
</body>
</html>
