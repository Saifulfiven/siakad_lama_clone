<?php
session_start();

$id_user = '';

if ( isset($_GET['dsn']) ) {
	$_SESSION['idDosen'] = $_GET['dsn'];
} else {
	$id_user = $_SESSION['idDosen'];
}