<?php
$file = 'https://siakad.stienobel-indonesia.ac.id/login';
$file_headers = @get_headers($file);

if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
    $exists = false;
}
else {
    $exists = true;
}

if ( !$exists ) {
	exec('apache-restart-aksi.bat');
} else {
	print('Server already running');
}

?>