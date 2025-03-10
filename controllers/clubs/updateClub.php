<?php
error_reporting(E_ALL);
include('connector.php');
session_start();

function setResponse($state, $message){
    $response = 
    [
        'state' => $state,
        'message' => $message
    ];
    echo (json_encode($response));
  
  }

$user = $_SESSION['user'];
if(!empty($_POST['club']))
{
$club = $_POST['club']; 
$sql = "SELECT * FROM clubs WHERE name='$club' AND founder='$user'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) === 1) {
  
if(!empty($_POST['card']))
{
    if($_POST['card'] === 'pumpkin'){
        $card = 'pumpkin';
    }//GEREKSIZ, DIREKT SET YAP DEĞERI
    else if($_POST['card'] === 'cardinal'){
        $card = 'cardinal';
    }
    else if($_POST['card'] === 'night'){
        $card = 'night'; 
    }
    else if($_POST['card'] === 'pacific'){
        $card = 'pacific';
    }
    else if($_POST['card'] === 'green'){
        $card = 'green';
    }
    else {
        $card = 'crumbs';
    }
    $sql = "UPDATE clubs SET card='$card' WHERE founder='$user' AND name='$club'"; //IMPROVE THIS OPTIOAL, BUT HANDLE PARAMETERS IN SEARCH
    $result = mysqli_query($conn, $sql);

}
if(!empty($_POST['description']))
{
 
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $sql = "UPDATE clubs SET description='$description' WHERE founder='$user' AND name='$club'"; //IMPROVE THIS OPTIOAL, BUT HANDLE PARAMETERS IN SEARCH
    $result = mysqli_query($conn, $sql);

}


if (!empty($_FILES['photo'])) {

    $directory = $_SERVER["DOCUMENT_ROOT"] . "/club-images/";
    $newName = basename($club . '-' . $_FILES["photo"]["name"]);
    $file = $directory . $newName;
    $filetype = strtolower(pathinfo($file,PATHINFO_EXTENSION));
    if ($_FILES["photo"]["size"] < 1200000) {
        if(in_array($filetype, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) {

             
      
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $file)) {
        
            $photo = $newName;
            $sql = "UPDATE clubs SET photo='$photo' WHERE founder='$user' AND name='$club'";
            $result = mysqli_query($conn, $sql);
            }
} else {
$state= 'error';
$message = 'File format is not supported.';//Imp
setResponse($state, $message);
exit;
}
} else {
$state= 'error';
$message = 'File should be smaller than 1100kb.';//Imp
setResponse($state, $message);
exit;
}
}

$state= 'success';
$message = 'Club updated.';//Imp
setResponse($state, $message);
}
else {
    $state= 'error';
    $message = 'You can only edit your clubs.';//Imp
    setResponse($state, $message);
}
}

?>
