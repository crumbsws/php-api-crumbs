<?php
include('connector.php');
include('library.php');
session_start();

function setResponse($state, $message){
    $response = 
    [
        'state' => $state,
        'message' => $message
    ];
    echo (json_encode($response)); 
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if(!empty($data['user']) && !empty($data['password']) && !empty($data['email']))
{
  $user = str_replace(' ', '', $data['user']);
  $password = $data['password'];
  $email = $data['email'];
  $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $sql = "SELECT * FROM account WHERE user = '$user' OR email = '$sanitizedEmail'";
  $result = mysqli_query($conn, $sql);
  if(mysqli_num_rows($result) === 0) {
    $sql = "INSERT INTO account (user, email, password) VALUES ('$user', '$sanitizedEmail', '$hashedPassword')";
    mysqli_query($conn, $sql);
    createProfile($conn, $user, 1);
    createToken($conn, $user);
    $_SESSION['user'] = $user;
    $message = 'Created account';
    $state = 'loggedin';


    setResponse($state, $message);


  }
  else
  {
    $state = 'error';
    $message = 'An account with that name/mail already exists.';
    setResponse($state, $message);
  }

}
else
{
  $state = 'error';
  $message = 'Please fill everything.';
  setResponse($state, $message);
}
?>
