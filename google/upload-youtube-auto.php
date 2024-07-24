<?php
error_reporting(0);

$key = file_get_contents('key.txt');
 
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
  throw new Exception(sprintf('Please run "composer require google/apiclient:~2.0" in "%s"', __DIR__));
}
require_once __DIR__ . '/vendor/autoload.php';

require_once 'config-ku.php';

$data = json_decode(file_get_contents('php://input'), true);

if ( isset($data['dsn']) ) {

    $id = $data['id'];
    $id_dosen = $data['dsn'];
    $judul = $data['judul'];
    $file = $data['file'];
    $nama_dosen = $data['dosen'];

    $application_name = 'LMS Nobel'; 
    $client_secret = 'PPGMllei8atEfNkkG_67QKmJ';
    $client_id = '1030142492659-argql2jni5rrvc1dnjbgtgbgqgqjh2c5.apps.googleusercontent.com';
    $scope = array('https://www.googleapis.com/auth/youtube.upload', 'https://www.googleapis.com/auth/youtube', 'https://www.googleapis.com/auth/youtubepartner');
    $path = 'E:\data-video-lms';

    $videoPath = $path.'/'.$id_dosen.'/'.$file;
    $videoTitle = $judul;
    $videoDescription = 'Materi video dari '.$nama_dosen;
    $videoCategory = "27";
    $videoTags = array($nama_dosen);


    if ( empty($file) ) {
        header("HTTP/1.0 422 File tidak ditemukan");
        exit;
    }

    try{
        // Client init
        $client = new Google_Client();
        $client->setApplicationName($application_name);
        $client->setClientId($client_id);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setAccessToken($key);
        $client->setScopes($scope);
        $client->setClientSecret($client_secret);
     
        if ($client->getAccessToken()) {
     
            /**
             * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
             */

            if($client->isAccessTokenExpired()) {
                $newToken = json_decode(json_encode($client->getAccessToken()));
                $client->refreshToken($newToken->refresh_token);
                file_put_contents('key.txt', json_encode($client->getAccessToken()));
            }
     
            $youtube = new Google_Service_YouTube($client);
     
            // Create a snipet with title, description, tags and category id
            $snippet = new Google_Service_YouTube_VideoSnippet();
            $snippet->setTitle($videoTitle);
            $snippet->setDescription($videoDescription);
            $snippet->setCategoryId($videoCategory);
            $snippet->setTags($videoTags);
     
            // Create a video status with privacy status. Options are "public", "private" and "unlisted".
            $status = new Google_Service_YouTube_VideoStatus();
            $status->setPrivacyStatus('unlisted');
     
            // Create a YouTube video with snippet and status
            $video = new Google_Service_YouTube_Video();
            $video->setSnippet($snippet);
            $video->setStatus($status);
     
            // Size of each chunk of data in bytes. Setting it higher leads faster upload (less chunks,
            // for reliable connections). Setting it lower leads better recovery (fine-grained chunks)
            $chunkSizeBytes = 1 * 1024 * 1024;
     
            // Setting the defer flag to true tells the client to return a request which can be called
            // with ->execute(); instead of making the API call immediately.
            $client->setDefer(true);
     
            // Create a request for the API's videos.insert method to create and upload the video.
            $insertRequest = $youtube->videos->insert("status,snippet", $video);
     
            // Create a MediaFileUpload object for resumable uploads.
            $media = new Google_Http_MediaFileUpload(
                $client,
                $insertRequest,
                'video/*',
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($videoPath));
     
     
            // Read the media file and upload it chunk by chunk.
            $status = false;
            $handle = fopen($videoPath, "rb");
            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }
     
            fclose($handle);
     
            /**
             * Video has successfully been upload, now lets perform some cleanup functions for this video
             */

            if ($status->status['uploadStatus'] == 'uploaded') {
                // Actions to perform for a successful upload
                $uploaded_video_id = $status['id'];
                // $_SESSION['video'] = $uploaded_video_id;

                $query = "UPDATE lms_video SET video_id='$uploaded_video_id', uploaded='y' where id=$id";
                $update = $conn->query($query);
                // echo 'Sukses';
                header("HTTP/1.0 422 Update info: ".$id.'-'.$update);
                exit;
            } else {
                header("HTTP/1.0 422 Gagal mengupload video");
                exit;
            }
     
            // If you want to make other calls after the file upload, set setDefer back to false
            $client->setDefer(false);
     
        } else{
            // @TODO Log error
            header("HTTP/1.0 422 Problems creating the client");
            exit;
        }
     
    } catch(Google_Service_Exception $e) {
        // print "Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage();
        // print "Stack trace is ".$e->getTraceAsString();
        if ( $e->getCode() == '403' ) {
            header("HTTP/1.0 422 Kuota harian penggunaan API Nobel telah habis. Lakukan lagi besok.");
        } else {
            header("HTTP/1.0 422 Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage());
        }

        exit;
    }catch (Exception $e) {
        // print "Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage();
        // print "Stack trace is ".$e->getTraceAsString();
        if ( $e->getCode() == '403' ) {
            header("HTTP/1.0 422 Kuota harian penggunaan API Nobel telah habis. Lakukan lagi besok.");
        } else {
            header("HTTP/1.0 422 Caught Google service Exception ".$e->getCode(). " message is ".$e->getMessage());
        }
        exit;
    }

}