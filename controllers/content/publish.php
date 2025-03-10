<?php
session_start();
error_reporting(E_ALL);
include('connector.php');
include('config.php');
include('library.php');
function setResponse($state, $url){
  $response =  
  [
      'id' => $url,
      'state' => $state
  ];
  echo (json_encode($response));

}
$user = $_SESSION['user'];
$date = date("Y-m-d h:i");

if($_POST['category'] === 'post') {
if (isset($_POST['title']) && !empty($_POST['body']) && isset($_POST['collect']) && !empty($user)) {

$url = uniqid();

$directory = $_SERVER["DOCUMENT_ROOT"] . "/images/";

if (isset($_FILES['conf'])) {
    $newName = basename($url . '-' . $_FILES["conf"]["name"]);
    $file = $directory . $newName;
    if (move_uploaded_file($_FILES["conf"]["tmp_name"], $file)) {
        $conf = $newName;
    }
    else {
        $state= 'error';
        setResponse($state, []);
        exit;
        }
}
else {
    $conf = null;
}

if(isset($_POST['access'])){
if($_POST['access'] === 'public' || $_POST['access'] === 'friends'){
$access = $_POST['access'];
}
else {
$access = 'public';
}
}
else {
$access = 'public';
}


$title = mysqli_real_escape_string($conn, $_POST['title']);
$collect = mysqli_real_escape_string($conn, $_POST['collect']);



$body = mysqli_real_escape_string($conn, $_POST['body']);

if(!empty($_POST['parent'])){
  $parent = mysqli_real_escape_string($conn, $_POST['parent']);
  $sql = "SELECT name, body, title FROM paths WHERE url='$parent'";
  $result = mysqli_query($conn, $sql);
  if(mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    $parentName = mysqli_real_escape_string($conn, $row['name']);
    $parentTitle = mysqli_real_escape_string($conn, $row['title']);
    $parentBody = mysqli_real_escape_string($conn, $row['body']);
    if($parentName !== $user){
        if($parentTitle){
        $message = $user . ' replied to your post: ' . $parentTitle;
        }
        else {
        $message = $user . ' replied to your post: ' . $parentBody;
        }
    createSystemMessage($conn, $parentName, $message);
    }
    }
  else {
        $parent = 'public';
    }
    
}
else {
    $parent = 'public';
}

    $sql = "INSERT INTO paths (name, title, parent, url, body, date, conf, collect, access) VALUES ('$user', '$title', '$parent', '$url', '$body', '$date', '$conf', '$collect', '$access')";
    $state= 'success';
    setResponse($state, $url);
    addPoint($conn, $user, $postPoint);
    mysqli_query($conn, $sql);
    mysqli_close($conn);

}
else {
    $state= 'error';
    setResponse($state, []);
    }

} else if($_POST['category'] === 'pin') {
    if (!empty($_POST['url']) && !empty($_POST['club']) && !empty($_POST['category']) && !empty($_SESSION['user'])) {
        if($_POST['type'] === 'post'){
            $club = mysqli_real_escape_string($conn, $_POST['club']);
            $type = mysqli_real_escape_string($conn, $_POST['type']);
            $url = mysqli_real_escape_string($conn, $_POST['url']);
            $sql = "SELECT * FROM club_user WHERE club='$club' AND user='$user'";
            $result = mysqli_query($conn, $sql);

            if(isset($_POST['quote'])){
                $quote = mysqli_real_escape_string($conn, $_POST['quote']);
            }
            else {
                $quote = null;
            }

            if(mysqli_num_rows($result) === 1) {
                $sql = "INSERT INTO pins (name, quote, type, url, club, date) VALUES ('$user', '$quote', '$type', '$url', '$club', '$date')";
                if(mysqli_query($conn, $sql)){
                    $state= 'success';
                    
                    setResponse($state, $url);
                    mysqli_close($conn);
                }
                else {
                    $state= 'error';
                    setResponse($state, []);
                } 
            }
            else {
                $state= 'error';
                setResponse($state, []);
            }
        }
        else if($_POST['category'] === 'note'){
            error_log('Not released yet');
            exit;
        }
}} else {
    $state= 'error';
    setResponse($state, []);
}
?>