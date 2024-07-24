<?php
function sendMail($nim, $id)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "http://192.168.30.100:8080/api/mail-pembayaran-sukses/$nim/$id?token=bf0b9355-0aad-477b-b131-b85502cd6556");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	$response = curl_exec($ch);
	curl_close($ch);

}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://192.168.30.100:8080/api/mhs/pembayaran/cek?token=bf0b9355-0aad-477b-b131-b85502cd6556');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

$response = curl_exec($ch);
curl_close($ch);

$res = json_decode($response);

if ( count($res->mhs) > 0 ) {

	foreach( $res->mhs as $val ) {
		sendMail($val->nim, $val->id);
	}
}
echo '<pre>';
print_r($res);
echo '</pre>';
?>