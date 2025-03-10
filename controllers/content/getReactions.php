<?php

session_start();
include('connector.php');
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if(isset($data['id'])){
    $id = $data['id'];
    $sql = "SELECT reaction.rating, reaction.name, profile.photo FROM reaction INNER JOIN profile on profile.name=reaction.name WHERE url='$id'";
    $result = mysqli_query($conn, $sql);
    $response = array();
    
    while($row = mysqli_fetch_assoc($result)) {
        $response[] = $row;
    }

}
else{
    $response[] = 0;
}    
    
echo (json_encode($response));

?>
