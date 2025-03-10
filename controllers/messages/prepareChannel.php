<?php
session_start();
error_reporting(E_ALL);
include('connector.php');
include('library.php');
function setResponse($state, $url){
  $response = 
  [
      'url' => $url,
      'state' => $state
  ];
  echo (json_encode($response));

}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if(!empty($data['user']))
{
$user1 = $_SESSION['user'];
$user2 = $data['user'];
$sql = "SELECT * FROM account WHERE user='$user2'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) === 1) {
    $sql = "SELECT * FROM channels WHERE url IN(SELECT url FROM channel_user WHERE user='$user1' AND url IN(SELECT url FROM channel_user WHERE user='$user2'))";
    $result = mysqli_query($conn, $sql); 
    if(mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $url = $row['url'];
        $state = 'success';//here
        setResponse($state, $url);
    }
    elseif (mysqli_num_rows($result) === 0) {
        $url = uniqid();
        $sql = "INSERT INTO channels (url) VALUES ('$url')";
        if(mysqli_query($conn, $sql)){
        $sql = "INSERT INTO channel_user (user, url) VALUES  ('$user1', '$url'), ('$user2', '$url')";
        if(mysqli_query($conn, $sql)){
        $state= 'success';
        setResponse($state, $url);
        }
        }
        else {
            $state= 'fail';
            setResponse($state);
        }  
    }
}
else {
    $state= 'fail';
    setResponse($state);
}  
}
?>