<?php
error_reporting(E_ALL);
include('connector.php');
include('config.php');
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


if(!empty($_POST['name']))
{
    $name = str_replace(' ', '', $_POST['name']);
    $sql = "SELECT * FROM clubs WHERE name='$name'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) === 0) {
    $founder = $_SESSION['user']; 
    $description = 'The club of ' . $founder;
    createClub($conn, $name, $founder, $description, 'crumbs', $clubInitial);
    setClub($conn, $founder, $name);
    $state= 'success';
    setResponse($state, $name, '');
    }
    else {
    $state= 'fail';
    setResponse($state, $name, 'Club already exist.');
    }
    }




?>
