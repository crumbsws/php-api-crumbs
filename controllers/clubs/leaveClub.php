<?php
error_reporting(E_ALL);
include('connector.php');

session_start();
include('library.php');
function setResponse($state, $name, $message){
    $response = 
    [
        'state' => $state,
        'name' => $name,
        'message' => $message
    ];
    echo (json_encode($response));
  
  } 

$json = file_get_contents('php://input');
$data = json_decode($json, true);


if(!empty($data['club']) && !empty($_SESSION['user']))
{
    $club = $data['club'];
    $user = $_SESSION['user'];
    leaveClub($conn, $user, $club);
    $state = 'success' ;
    $message = 'Left the club'; ;
    setResponse($state, $club, $message);
    
}




?>
