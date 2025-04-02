<?php
session_start();

require 'C:/xampp/htdocs/Ginhawa/vendor/autoload.php';

use Google\Client;

$client = new Client();
$client->setApplicationName('Ginhawa Meet');
$client->setScopes([Google\Service\Calendar::CALENDAR]);
$client->setAuthConfig('C:/xampp/htdocs/Ginhawa/credentials.json');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
$client->setRedirectUri('http://localhost/Ginhawa/patient/callback.php'); // Must match Google Cloud Console

// Handle the OAuth callback
if (isset($_GET['code'])) {
    $authCode = $_GET['code'];
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
    $client->setAccessToken($accessToken);

    // Save the token
    $tokenPath = 'C:/xampp/htdocs/Ginhawa/token.json';
    if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));

    // Redirect back to booking-complete.php
    header("Location: http://localhost/Ginhawa/patient/booking-complete.php");
    exit;
} else {
    echo "Authorization failed. No code received.";
    exit;
}
?>