<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Activation de votre compte</title>
</head>
<body>
    <h1>Activez votre compte</h1>

    <p>Bonjour,</p>

    <p>Vous recevez cet e-mail car un compte a été créé avec votre adresse.</p>

    <p>Pour activer votre compte, veuillez cliquer sur le lien ci-dessous :</p>

    <a href="{{ url('api/account.activate', $token) }}">Activer mon compte</a>

    <p>Ce lien restera valide pour une durée limitée.</p>

    <p>Si vous n'avez pas créé ce compte, veuillez ignorer cet e-mail.</p>

    <p>Cordialement,</p>

    <p>L'équipe de notre site</p>
</body>
</html>
