<?php
error_reporting(E_ALL);
include ('connector.php');
session_start();
function setResponse($state, $message){
    $response = 
    [
        'state' => $state,
        'message' => $message
    ];
    echo (json_encode($response));
  
  }
if (!empty($_FILES['asset']['name'][0])) {
    foreach( $_FILES['asset']['name'] as $key => $name) {
   
    
    $directory = $_SERVER["DOCUMENT_ROOT"] . "/message-assets/";
    $newName = basename($name);
    $file = $directory . $newName;
    $filetype = strtolower(pathinfo($file,PATHINFO_EXTENSION));
    if ($_FILES["asset"]["size"][$key] < 10485760) {
        if(!in_array($filetype, ['exe', 'bat', 'rat'])) {

             
      
            if (move_uploaded_file($_FILES["asset"]["tmp_name"][$key], $file)) {
        
                $state= 'success';
                $message = 'File transfer succesful.';//Imp
                setResponse($state, $message);
                
                

            }
            else {
                $state= 'error';
                $message = 'File transfer failed.';//Imp
                setResponse($state, $message);
                
            }
} else {
$state= 'error';
$message = 'File format is not supported.';//Imp
setResponse($state, $message);
exit;
}
} else {
$state= 'error';
$message = 'File should be smaller than 10mb.';//Imp
setResponse($state, $message);
exit;
}
}
}else {
    $state= 'error';
    $message = 'No file.';//Imp
    setResponse($state, $message);
    exit;
    }
?>