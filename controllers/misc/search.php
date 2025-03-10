<?php
error_reporting(E_ALL);
include('connector.php');
session_start();

$json = file_get_contents('php://input');
$data = json_decode($json, true);
$value = $data['value'];
$type = $data['type'];
$name = $_SESSION['user'];
if(isset($data['value']) && isset($data['type']))
{
    if($type == 'people'){
    $sql = "SELECT * FROM profile WHERE name LIKE '%$value%' ORDER BY point DESC LIMIT 8"; 
    }
    else if($type == 'clubs'){ 
    $sql = "SELECT * FROM clubs WHERE name LIKE '%$value%' ORDER BY point DESC LIMIT 8"; 
    }
    else {
    $sql = "SELECT * FROM paths WHERE (title OR body OR name OR collect OR url LIKE '%$value%' OR body LIKE '%$value%' OR name LIKE '%$value%' OR collect LIKE '%$value%' OR url LIKE '%$value%')  AND (access = 'public' 
                         OR (access = 'friends' AND name IN 
                            (SELECT CASE 
                                    WHEN user_2 = '$name' THEN user_1 
                                    ELSE user_2 
                                    END AS friend 
                             FROM friends 
                             WHERE user_1 = '$name' 
                             OR user_2 = '$name'))) ORDER BY date DESC LIMIT 8";
    }
    
    $result = mysqli_query($conn, $sql);
    $data = array();
    
    while($row = mysqli_fetch_array($result)) {
        $data[] = $row;
    }

}
echo (json_encode($data));
?>
