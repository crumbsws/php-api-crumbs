<?php
session_start();
include('connector.php');
include('library.php');
include('mail-library.php');
include('config.php');
$json = file_get_contents('php://input');
$data = json_decode($json, true);
function setResponse($state, $message){
    $response = 
    [
        'state' => $state,
        'message' => $message
    ];
    echo (json_encode($response));
  }

  
  if(isset($data['identifier']) && !empty($data['identifier'])) {
    $input = $data['identifier'];
    $sql = "SELECT email, user FROM account WHERE user='$input' OR email='$input'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) === 1) {

        $row = mysqli_fetch_array($result);
        $email = $row['email'];
        $alias = $row['user'];
        $code = rand(100000, 999999);
        createResetCode($conn, $input, $code);


        sendPRCode($code, $email, $alias, $smtpUser, $smtpPassword, $smtpServ);






        $state = 'success';
        $message = '';
        setResponse($state, $message);
        //lol
    }
    else {
        $state = 'error';
        $message = 'No accounts found with that address.';
        setResponse($state, $message);      
    }
}

if(isset($data['code']) && !empty($data['code']) && isset($data['password']) && !empty($data['password'])) {
    $code = $data['code'];
    $password = $data['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $date = date('Y-m-d h:i');
    $sql = "SELECT user FROM reset_code WHERE code='$code' AND expiry > '$date' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $value = $row['user'];
        
        $sql = "UPDATE account SET password='$hashedPassword' WHERE user='$value' OR email='$value'";
        if(mysqli_query($conn, $sql)){
        resetResetCode($conn, $value, $code);
        $state = 'success';
        $message = '';
        setResponse($state, $message);
        }
        else {
            $state = 'error';
            $message = 'Could not update credentials.';
            setResponse($state, $message);      
        } 
    }
    else {
        $state = 'error';
        $message = 'Code is invalid.';
        setResponse($state, $message);      
    }
}
?>