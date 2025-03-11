<?php
require_once '../../libraries/library.php'
require_once '../../initials/connector.php'
class AuthController {


private function setResponse() {
  setResponse($state, $data, $clubs, $contacts){
      $response =
      [
          'state' => $state,
          'data' => $data,
          'clubs' => $clubs,
          'contacts' => $contacts

      ];
      echo (json_encode($response));
      exit;

}

public function authenticate() {
  $user = checkToken($conn);
  //

  if ($user) {

      $_SESSION['user'] = $user;
      error_log("User from session: " . $_SESSION['user']);
      error_log("User from token: " . $user);
      $sql = "SELECT * FROM profile WHERE name='$user'";
      if($result = mysqli_query($conn, $sql)){
          if(mysqli_num_rows($result) === 1) {
              $data = array();
              while ($row = mysqli_fetch_assoc($result)) {
              $data[] = $row;
              }
              $contacts = getContacts($conn, $user);
              $clubs = getClub($conn, $user);
              $state = 'success';
              setResponse($state, $data, $clubs, $contacts);
          }
          else {

          $state = 'error';
          $data = [];
          $clubs = [];
          $contacts = [];
          setResponse($state, $data, $clubs, $contacts);
          }
      }
      else {

          $state = 'error';
          $data = [];
          $clubs = [];
          $contacts = [];
          setResponse($state, $data, $clubs, $contacts);
          }
      }
      else {

          $state = 'error';
          $data = [];
          $clubs = [];
          $contacts = [];
          setResponse($state, $data, $clubs, $contacts);
          }
}

public function login() {
  $user = $data['user'];
  $password = $data['password'];

  $sql = "SELECT * FROM account WHERE user='$user'";
  $result = mysqli_query($conn, $sql);
  if(mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    if(password_verify($password, $row['password']))
    {
    $_SESSION['user'] = $user;
    createToken($conn, $user);
    $message = 'Logged in';
    $state = 'loggedin';

    setResponse($state, $message);
    }
    else
    {
      $message = 'Wrong password, try again.';
      setResponse('error', $message);
    }
  }
  else
  {
    $message = 'Account not found, try again.';
    setResponse('error', $message);
  }

}
else
{
  $state = 'error';
  $message = 'Please fill everything.';
  setResponse($state, $message);
}
}


}


?>
