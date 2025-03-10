<?php
session_start();
include('connector.php');

include('library.php');




$user = $_SESSION['user'];
if(isset($_GET['status']) && $_GET['status'] === 'unseen') {
    $data = getUnseenSystemMessages($conn, $user);
  }
else {
    updateSystemMessages($conn, $user);
    $data = getSystemMessages($conn, $user);
}


    
echo (json_encode($data));
?>