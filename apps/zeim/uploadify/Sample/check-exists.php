<?php require_once dirname(dirname(__DIR__)).'/configuracion/config.php'; require dirname(dirname(__DIR__)).'/configuracion/autentifica.php'; app_same_origin_guard(); ?>
<?php
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

// Define a destination
$targetFolder = '/uploads'; // Relative to the root and should match the upload folder in the uploader script

if (file_exists($_SERVER['DOCUMENT_ROOT'] . $targetFolder . '/' . $_POST['filename'])) {
	echo 1;
} else {
	echo 0;
}
?>