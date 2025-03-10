<?php
session_start();
include('connector.php');


$sql = "SELECT collect, COUNT(*) as count FROM paths WHERE date >= NOW() - INTERVAL 1 DAY GROUP BY collect ORDER BY count DESC LIMIT 5";
$result = mysqli_query($conn, $sql);
$data = array();

while($row = mysqli_fetch_array($result)) {
    $data[] = $row;
}

echo (json_encode($data));
?>