<?php
error_reporting(E_ALL);
include('connector.php');
 
session_start();
include('library.php');

    $user = $_SESSION['user'];
  if(isset($_GET['ownedBy']) && $_GET['ownedBy'] === 'true') {
    $data = getOwnedClub($conn, $user);
  }
  else if(isset($_GET['name'])) {
    $name = $_GET['name'];
    $data = getOtherClub($conn, $name);
  }
  else if(isset($_GET['user'])){
    $user = $_GET['user'];
    $data = getClub($conn, $user);
  }
  else {
$data = getClub($conn, $user);
}

echo (json_encode($data));


?>
