<?php 
include('config.php');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Origin: http://localhost:3000'); // change before deployment
header('Access-Control-Allow-Headers: Content-Type');
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, 'defaultdb');
?>

