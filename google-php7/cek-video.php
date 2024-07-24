<?php
include "config-ku.php";

$sql = $conn->query("SELECT * FROM lms_video where siap='n'");

$jml = $sql->num_rows;

if ( $jml > 0 ) {

	while( $val = $sql->fetch_object() ) {
		
		$file = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id='.$val->video_id.'&key=AIzaSyDc9iufW4i4FjwkOYXz_OctNKBho_gwZzU');
		$data = json_decode($file);

		if ( $data->pageInfo->totalResults == 1 ) {
			$update = $conn->query("UPDATE lms_video set siap='y' where id=$val->id");
		}
	}

}

print('Selesai Mengecek '. $jml .' video');



// Cek video yang gagal diupload ke youtube
$query = "SELECT v.*, d.nm_dosen FROM lms_video as v
left join dosen as d on v.id_dosen = d.id where uploaded='n' and file is not null limit 1";

$sql = $conn->query($query);

$video = $sql->fetch_object();
if ( !empty($video) ) {

	$data = [
		'id' => $video->id,
		'dsn' => $video->id_dosen,
		'dosen' => $video->nm_dosen,
		'judul' => $video->judul,
		'file' => $video->file
	];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'http://siakad.test/google/upload-youtube-auto.php');

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

	$response = curl_exec($ch);
	curl_close($ch);

	echo '<pre>';
	print_r($response);
	echo '</pre>';

}