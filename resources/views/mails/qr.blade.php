<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 20px;
            margin-top: 0;
            color: #333;
        }

        p {
            font-size: 16px;
            color: #666;
        }

        .qr-code {
            display: block;
            margin: 20px auto;
        }

        .qr-code img {
            width: 100%;
            height: auto;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Bonjour {{ $username }},</h1>
        <p>Voici votre QR Code :</p>
        <div class="qr-code">
            <img src="data:image/png;base64, {!! base64_encode($qrCode) !!}">
        </div>
        <p class="footer">Merci,<br>Votre équipe</p>
    </div>
</body>

</html>
