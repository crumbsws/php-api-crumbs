<?php
session_start();
include('connector.php');
include('library.php');
$json = file_get_contents('php://input');
$data = json_decode($json, true);

function setResponse($state, $message){
    $response = 
    [
        'state' => $state,
        'message' => $message
    ];
    echo (json_encode($response));
  
  }

if(isset($data['user']) && isset($_SESSION['user']))
{
    $sender = $data['user'];
    $receiver = $_SESSION['user'];
    $date = date("Y-m-d h:i:sa");
    $sql = "SELECT * FROM requests WHERE sender = '$sender' AND receiver = '$receiver'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) === 1) {
      $sql = "INSERT INTO friends (user_1, user_2, level) VALUES ('$sender', '$receiver', 0)";
      if(mysqli_query($conn, $sql)){
      $sql = "UPDATE requests SET status='accepted' WHERE sender = '$sender' AND receiver = '$receiver'";
      mysqli_query($conn, $sql);

      $message = 'You are now friends with ' . $receiver;
      createSystemMessage($conn, $sender, $message);

      $message = 'Accepted ' . $sender;
      $state = 'success';
      setResponse($state, $message);
    }
  
    }
    else
    {
      $state = 'error';
      $message = 'No request';
      setResponse($state, $message);
    }
}
?>
