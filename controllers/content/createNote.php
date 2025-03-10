<?php
include('connector.php');
include('library.php');
session_start();
function setResponse($state, $message){
    $response = 
    [
        'state' => $state,
        'message' => $message
    ];
    echo (json_encode($response));
  }

if(!empty($_POST['note']) && !empty($_POST['club'])) 
{
    $user = $_SESSION['user'];
    $amount = 1;
    $date = date("Y-m-d h:i");
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $club = mysqli_real_escape_string($conn, $_POST['club']);
    $sql = "INSERT INTO gossip (name, note, club, date) VALUES ('$user', '$note', '$club', '$date')";
    if(mysqli_query($conn, $sql)){
    $state= 'success';
    $message= 'Note Sent';
    setResponse($state, $message);
    addPoint($conn, $user, $amount);
    }
    else {
        $state= 'fail';
        $message= 'Failed';
        setResponse($state, $message);
    }  

}
?>
