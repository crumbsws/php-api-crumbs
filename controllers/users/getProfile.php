<?php
error_reporting(E_ALL);
include('connector.php');
session_start();
include('library.php');

$json = file_get_contents('php://input');
$data = json_decode($json, true);



if(isset($data['user']))
{
    $user = $data['user'];
    $sql = "SELECT profile.*, diary.message
FROM profile
LEFT JOIN diary ON diary.name = profile.name
WHERE profile.name = '$user'
ORDER BY date DESC
LIMIT 1;";

//get the latest diary message
$result = mysqli_query($conn, $sql);
$data = array();
while($row = mysqli_fetch_array($result)) {
  $data[] = $row;
}

}
echo (json_encode($data));


?>
