<?php

session_start();
include('connector.php');
$json = file_get_contents('php://input');
$data = json_decode($json, true);


    $user = $_SESSION['user']; 
    $sql = "SELECT profile.name, profile.photo, channel_user.url AS channel, messages.message, messages.user, messages.date, messages.status FROM channel_user 
            JOIN profile ON profile.name = channel_user.user JOIN messages ON messages.channel = channel_user.url
            WHERE profile.name != '$user'
            AND messages.id = (
            SELECT MAX(messages.id)
            FROM messages
            WHERE messages.channel = channel_user.url AND messages.channel IN(SELECT url FROM channel_user WHERE user='$user')
            )
            
            ORDER BY 
            messages.id DESC;";
    $result = mysqli_query($conn, $sql);
    $response = array();
    
    while($row = mysqli_fetch_assoc($result)) {
        $response[] = $row;
    }

  
    
echo (json_encode($response));

?>
