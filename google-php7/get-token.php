<?php
session_start();

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
  throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
require_once __DIR__ . '/vendor/autoload.php';

$OAUTH2_CLIENT_ID = '1030142492659-argql2jni5rrvc1dnjbgtgbgqgqjh2c5.apps.googleusercontent.com';
$OAUTH2_CLIENT_SECRET = 'PPGMllei8atEfNkkG_67QKmJ';
$REDIRECT = 'http://siakad.test/google/get-token.php';
$APPNAME = "Nobel";

$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$client->setRedirectUri($REDIRECT);
$client->setApplicationName($APPNAME);
$client->setAccessType('offline');
$client->setPrompt('consent');
 
 
// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);
 
if (isset($_GET['code'])) {
    if (strval($_SESSION['state']) !== strval($_GET['state'])) {
        die('The session state did not match.');
    }
 
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
 
}
// print_r($client->getAccessToken());
// exit;
if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
    echo '<code>';
    $token = json_encode($_SESSION['token']);
    file_put_contents('key.txt', $token);
    echo '</code>';
}

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
    try {
        // Call the channels.list method to retrieve information about the
        // currently authenticated user's channel.
        $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
            'mine' => 'true',
        ));
 
        $htmlBody = '';
        foreach ($channelsResponse['items'] as $channel) {
            // Extract the unique playlist ID that identifies the list of videos
            // uploaded to the channel, and then call the playlistItems.list method
            // to retrieve that list.
            $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];
 
            $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
                'playlistId' => $uploadsListId,
                'maxResults' => 50
            ));
 
            $htmlBody .= "<h3>Videos in list $uploadsListId</h3><ul>";
            foreach ($playlistItemsResponse['items'] as $playlistItem) {
                $htmlBody .= sprintf('<li>%s (%s)</li>', $playlistItem['snippet']['title'],
                    $playlistItem['snippet']['resourceId']['videoId']);
            }
            $htmlBody .= '</ul>';
        }
    } catch (Google_ServiceException $e) {
        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    } catch (Google_Exception $e) {
        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    }
 
    $_SESSION['token'] = $client->getAccessToken();
} else {
    $state = mt_rand();
    $client->setState($state);
    $_SESSION['state'] = $state;
 
    $authUrl = $client->createAuthUrl();
    $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorise access</a> before proceeding.<p>
END;
}

?>
 
<!doctype html>
<html>
<head>
    <title>My Uploads</title>
</head>
<body>
<?php echo $htmlBody?>
</body>
</html>