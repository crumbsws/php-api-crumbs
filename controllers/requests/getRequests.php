<?php
session_start();
include('connector.php');

include('library.php');




$user = $_SESSION['user'];
if(isset($_GET['status']) && $_GET['status'] === 'unseen') {
    $data = getUnseenRequests($conn, $user);
  }
else {
    updateRequests($conn, $user);
    $data = getRequests($conn, $user);
}


    
echo (json_encode($data));
?>