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

$user = $_POST['user'];

if (!empty($_FILES['photo'])) {

    $directory = $_SERVER["DOCUMENT_ROOT"] . "/profile-images/";
    $newName = basename($user . '-' . $_FILES["photo"]["name"]);
    $file = $directory . $newName;
    $filetype = strtolower(pathinfo($file,PATHINFO_EXTENSION));
    if ($_FILES["photo"]["size"] < 1200000) {
        if(in_array($filetype, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) {

             
      
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $file)) {
        
            $photo = $newName;
            $sql = "UPDATE profile SET photo='$photo' WHERE name='$user'";
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

if(!empty($_POST['relationship']) && $_POST['relationship'] != '')
{
    if($_POST['relationship'] === 'yes'){
        $relationship = 'Single';
    }
    else if($_POST['relationship'] === 'no'){
        $relationship = 'In a relationship';
    }
    else {
        $relationship = 'Has no idea';
    }
    $sql = "UPDATE profile SET relation='$relationship' WHERE name='$user'"; 
    $result = mysqli_query($conn, $sql);

}
if(!empty($_POST['description']) && $_POST['description'] != '')
{
 
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $sql = "UPDATE profile SET description='$description' WHERE name='$user'"; 
    $result = mysqli_query($conn, $sql);
    
}
if(!empty($_POST['home']) && $_POST['home'] != '')
{
 
    $home = mysqli_real_escape_string($conn, $_POST['home']);
    $sql = "UPDATE profile SET home='$home' WHERE name='$user'"; 
    $result = mysqli_query($conn, $sql);

}
$state= 'success';
$message = 'Profile updated.';//Imp
setResponse($state, $message);
?>