<?php
require_once 'discord.php';

// Replace these with your Discord application credentials
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET'; #
$redirectUri = 'YOUR_REDIRECT_URL';

$discordSSO = new DiscordSSO($clientId, $clientSecret, $redirectUri);

if (isset($_GET['code'])) {
    $accessToken = $discordSSO->getAccessToken($_GET['code']);

    if ($accessToken) {
        $userInfo = $discordSSO->getUserInfo($accessToken);
        print_r($userInfo);
    } else {
        echo 'Error: Failed to obtain access token.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Discord SSO Example</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<?php if (isset($userInfo)): ?>
    <h1>You are logged in <?= $userInfo['username'] ?></h1>
<?php else: ?>
    <h1>Discord SSO Example</h1>
    <a href="<?= $discordSSO->getLoginUrl() ?>" class="btn btn-primary">Log in with Discord</a>
<?php endif; ?>
</body>
</html>
