<?php

require_once './initials/conn.php';

class AuthController {

  

  private $conn;
  private $data;

  public function __construct()
  {
    $this->conn = new Connector();
    $this->conn = $this->conn->connect();

    $this->data = json_decode(file_get_contents('php://input'), true);
  }

private function setResponse($state, $data, $clubs, $contacts){
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
  $user = $this->checkUserFromToken($this->conn);

  if ($user) {

      $_SESSION['user'] = $user;
      error_log("User from session: " . $_SESSION['user']);
      error_log("User from token: " . $user);
      $sql = "SELECT * FROM profile WHERE name='$user'";
      if($result = mysqli_query($this->conn, $sql)){
          if(mysqli_num_rows($result) === 1) {
              $this->data = array();
              while ($row = mysqli_fetch_assoc($result)) {
              $this->data[] = $row;
              }
              $contacts = getContacts($this->conn, $user);
              $clubs = getClub($this->conn, $user);
              $state = 'success';
              $this->setResponse($state, $this->data, $clubs, $contacts);
          }
          else {

          $state = 'error';
          $this->data = [];
          $clubs = [];
          $contacts = [];
          $this->setResponse($state, $this->data, $clubs, $contacts);
          }
      }
      else {

          $state = 'error';
          $this->data = [];
          $clubs = [];
          $contacts = [];
          $this->setResponse($state, $this->data, $clubs, $contacts);
          }
      }
      else {

          $state = 'error'; //user not set
          $this->data = [];
          $clubs = [];
          $contacts = [];
          $this->setResponse($state, $this->data, $clubs, $contacts);
          }
}










public function login() {
  if(!empty($this->data['user']) && !empty($this->data['password']))
  {
    $user = $this->data['user'];
    $password = $this->data['password'];
  
    $sql = "SELECT * FROM account WHERE user='$user'";
    $result = mysqli_query($this->conn, $sql);
    if(mysqli_num_rows($result) === 1) {
      $row = mysqli_fetch_assoc($result);
      if(password_verify($password, $row['password']))
      {
      $_SESSION['user'] = $user;
      $this->createToken($this->conn, $user);
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









private function createToken($conn, $name){
  $token = bin2hex(random_bytes(16));
  $expiry = date('Y-m-d h:i', strtotime('+30 days'));
  $sql = "INSERT INTO auth_token (user, token, expiry) VALUES ('$name', '$token', '$expiry')";
  setcookie('auth_token', $token, time() + (30 * 24 * 60 * 60), "/", "", true, true);

  mysqli_query($conn, $sql);
}
















private function checkUserFromToken($conn){
  if(isset($_COOKIE['auth_token'])){
    $token = $_COOKIE['auth_token'];
    $date = date('Y-m-d h:i');
    $sql = "SELECT user FROM auth_token WHERE token='$token' AND expiry > '$date'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);
    if (mysqli_num_rows($result) === 1) {
      return $row['user'];
      
    } else {
      error_log("No valid token found for: " . $token);
      return null;
    }
  } else {
  return null;
}

}


















private function clearToken($conn){
  if(isset($_COOKIE['auth_token'])){
    $token = $_COOKIE['auth_token'];
    $sql = "DELETE FROM auth_token WHERE token='$token'";
    $result = mysqli_query($conn, $sql);
  }
}












private function resetToken($conn, $user){

    $sql = "DELETE FROM auth_token WHERE user='$user'";
    $result = mysqli_query($conn, $sql);
}













private function createResetCode($conn, $name, $code){
  $expiry = date('Y-m-d h:i', strtotime('+2 days'));
  $sql = "INSERT INTO reset_code (user, code, expiry) VALUES ('$name', '$code', '$expiry')";

  mysqli_query($conn, $sql);
}











private function resetResetCode($conn, $name, $code){
  $sql = "DELETE FROM reset_code WHERE user='$name'";
  mysqli_query($conn, $sql);
}




}





?>
