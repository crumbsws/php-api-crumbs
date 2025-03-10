<?php
session_start();
error_reporting(E_ALL);
include('connector.php');
include('library.php');
function setResponse($state){
  $response = 
  [
      'state' => $state
  ];
  echo (json_encode($response));

}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if(!empty($data['user']) && !empty($data['channel']) && !empty($data['message']) && !empty($data['url']))
{
$user = mysqli_real_escape_string($conn, $data['user']);
$channel = mysqli_real_escape_string($conn, $data['channel']);
$message = mysqli_real_escape_string($conn, $data['message']);
$url = mysqli_real_escape_string($conn, $data['url']);
$date = date("Y-m-d h:i");
$amount = 1;
addPoint($conn, $user, $amount);
if(isset($data['reply'])) {
    $reply = $data['reply'];
}
else
{
    $reply = null;
}
if(!empty($data['assets'][0])) {
    $assets = $data['assets'];
    foreach ($assets as $x) {
        $sql = "INSERT INTO message_assets (parent, asset) VALUES ('$url', '$x')";
        mysqli_query($conn, $sql);
      }
}
//media handling too pls


$sql = "INSERT INTO messages (user, channel, url, message, reply, date, status) VALUES ('$user','$channel', '$url', '$message', '$reply', '$date', 'unseen')";
if(mysqli_query($conn, $sql)) {
    $state= 'success';
    setResponse($state);
}
else {
    $state= 'fail';
    setResponse($state);
}
}

?>