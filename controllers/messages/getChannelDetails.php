<?php
error_reporting(E_ALL);
include('connector.php');
session_start();
include('library.php');
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if(isset($data['channel'])){
    $channel = $data['channel'];
    $user = $_SESSION['user'];
    $sql = "SELECT * FROM profile WHERE name IN(SELECT user FROM channel_user WHERE user!='$user' AND url IN(SELECT url FROM channels WHERE url='$channel'))";
    $result = mysqli_query($conn, $sql);
    $data = array();
    
    while($row = mysqli_fetch_array($result)) {
        $data[] = $row;
    }
    

}
else{
    $data[] = 0;
}    
    
echo (json_encode($data));

?>
