<?php
session_start();
include('connector.php');
$json = file_get_contents('php://input');
$data = json_decode($json, true);

function setResponse($state, $name, $message){
    $response = 
    [
        'state' => $state,
        'name' => $name,
        'message' => $message
    ];
    echo (json_encode($response));
  
  }

if(isset($data['user']))
{
    $sender = $_SESSION['user'];
    $receiver = $data['user'];
    $date = date("Y-m-d h:i:sa");
    if($sender !== $receiver) {
      $sql = "SELECT * FROM requests WHERE sender = '$sender' AND receiver = '$receiver'";
      $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) === 0) {
      $sql = "INSERT INTO requests (sender, receiver, status, date) VALUES ('$sender', '$receiver', 'unseen', '$date')";
      mysqli_query($conn, $sql);
      $message = 'Sent to ' . $sender;
      $state = 'success';
      setResponse($state, $message, $_SESSION['user']);
  
    }
    }
    else
    {
      $state = 'error';
      $message = 'Already sent';
      setResponse($state, $message, []);
    }
}
?>
