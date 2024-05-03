<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification d'événement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
        }

        .event-details {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Notification d'événement</h1>
        <p>Cher membre, {{ $username }}</p>
        <p>Nous sommes heureux de vous informer de notre prochain événement :</p>
        <div class="event-details">
            <p><strong>Nom de l'événemeInt:</strong> {{ $evenement->titre }}  </p>
            <p><strong>Date:</strong> {{ $evenement->date }} </p>
            <p><strong>Lieu:</strong> {{ $evenement->lieu }} </p>
        </div>
        <p>Assurez-vous de noter cet événement dans votre calendrier ! </p>
        <p>Merci et à bientôt!</p>
    </div>
</body>

</html>
