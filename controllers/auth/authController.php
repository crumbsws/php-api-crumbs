<?php

require_once './initials/conn.php';
require_once './utils/response.php';

class AuthController {

  
  private $response;
  private $conn;
  private $data;

  protected $smtpUser = 'smtpUser'; // SMTP user
  protected $smtpPassword = 'smtpPassword'; // SMTP password
  protected $smtpServ = 'smtpServ'; // SMTP server
  protected $smtpPort = 'smtpPort'; // SMTP port



  public function __construct()
  {
    $this->conn = new Connector();
    $this->conn = $this->conn->connect();
    $this->response = new Response();
    $this->data = json_decode(file_get_contents('php://input'), true);
    session_start();
  }


public function authenticate() {
  $user = $this->checkUserFromToken();

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
              $contacts = 0;//getContacts($this->conn, $user);
              $clubs = 0;//getClub($this->conn, $user);
              $state = 'success';
              $this->response->send($state, '', ['data' => $this->data, 'clubs' =>$clubs, 'contacts' =>$contacts]);
          }
          else {

          $state = 'error';
          $this->data = [];
          $clubs = [];
          $contacts = [];
          $this->response->send($state, '', ['data' => $this->data, 'clubs' =>$clubs, 'contacts' =>$contacts]);
          }
      }
      else {

          $state = 'error';
          $this->data = [];
          $clubs = [];
          $contacts = [];
          $this->response->send($state, '', ['data' => $this->data, 'clubs' =>$clubs, 'contacts' =>$contacts]);
          }
      }
      else {

          $state = 'error'; //user not set
          $this->data = [];
          $clubs = [];
          $contacts = [];
          $this->response->send($state, '', ['data' => $this->data, 'clubs' =>$clubs, 'contacts' =>$contacts]);
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
      $this->createToken($user);
      $message = 'Logged in';
      $state = 'loggedin';
  
      $this->response->send($state, 'Logged in');
      }
      else
      {
        $message = 'Wrong password, try again.';
        $state = 'error';
        $this->response->send($state, $message);
      }
    }
    else
    {
      $message = 'Account not found, try again.';
      $state = 'error';
      $this->response->send($state, $message);
    }
  
  }
  else
  {
    $state = 'error';
    $message = 'Please fill everything.';
    $this->response->send($state, $message);
  }
}





//TODO: pass conn to sub-functions from the object not the inputs


public function register() {
if(!empty($this->data['user']) && !empty($this->data['password']) && !empty($this->data['email']))
{
  $user = str_replace(' ', '', $this->data['user']);
  $password = $this->data['password'];
  $email = $this->data['email'];
  $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $sql = "SELECT * FROM account WHERE user = '$user' OR email = '$sanitizedEmail'";
  $result = mysqli_query($this->conn, $sql); 
  if(mysqli_num_rows($result) === 0) {
    $sql = "INSERT INTO account (user, email, password) VALUES ('$user', '$sanitizedEmail', '$hashedPassword')";
    mysqli_query($this->conn, $sql);
    $this->createProfile( $user, 1); // create these
    $this->createToken($user);
    $_SESSION['user'] = $user;
    $message = 'Created account';
    $state = 'loggedin';


    $this->response->send($state, $message);


  }
  else
  {
    $state = 'error';
    $message = 'An account with that name/mail already exists.';
    $this->response->send($state, $message);
  }

}
else
{
  $state = 'error';
  $message = 'Please fill everything.';
  $this->response->send($state, $message);
}
}




public function resetPassword() {
  if(isset($this->data['identifier']) && !empty($this->data['identifier'])) { //manage data input entries
    $input = $this->data['identifier'];
    $sql = "SELECT email, user FROM account WHERE user='$input' OR email='$input'";
    $result = mysqli_query($this->conn, $sql);
    if(mysqli_num_rows($result) === 1) {

        $row = mysqli_fetch_array($result);
        $email = $row['email'];
        $alias = $row['user'];
        $code = rand(100000, 999999);
        createResetCode($input, $code); //add as priv func


        sendPRCode($code, $email, $alias, $this->smtpUser,  $this->smtpPassword,  $this->smtpServ); //add as priv func






        $state = 'success';
        $message = '';
        $this->response->send($state, $message);
        //lol
    }
    else {
        $state = 'error';
        $message = 'No accounts found with that address.';
        $this->response->send($state, $message);     
    }
}

if(isset($this->data['code']) && !empty($this->data['code']) && isset($this->data['password']) && !empty($this->data['password'])) {
    $code = $this->data['code'];
    $password = $this->data['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $date = date('Y-m-d h:i');
    $sql = "SELECT user FROM reset_code WHERE code='$code' AND expiry > '$date' LIMIT 1";
    $result = mysqli_query($this->conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $value = $row['user'];
        
        $sql = "UPDATE account SET password='$hashedPassword' WHERE user='$value' OR email='$value'";
        if(mysqli_query($this->conn, $sql)){
        resetResetCode($value, $code); //add as priv func
        $state = 'success';
        $message = '';
        $this->response->send($state, $message);
        }
        else {
            $state = 'error';
            $message = 'Could not update credentials.';
            $this->response->send($state, $message);    
        } 
    }
    else {
        $state = 'error';
        $message = 'Code is invalid.';
        $this->response->send($state, $message);     
    }
}
  }


public function logout() {
session_destroy();
$this->clearToken();
setcookie("auth_token", "", time()-3600);

}






private function createToken( $name){
  $token = bin2hex(random_bytes(16));
  $expiry = date('Y-m-d h:i', strtotime('+30 days'));
  $sql = "INSERT INTO auth_token (user, token, expiry) VALUES ('$name', '$token', '$expiry')";
  setcookie('auth_token', $token, time() + (30 * 24 * 60 * 60), "/", "", true, true);

  mysqli_query($this->conn, $sql);
}
















private function checkUserFromToken(){
  if(isset($_COOKIE['auth_token'])){
    $token = $_COOKIE['auth_token'];
    $date = date('Y-m-d h:i');
    $sql = "SELECT user FROM auth_token WHERE token='$token' AND expiry > '$date'";
    $result = mysqli_query($this->conn, $sql);
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


















private function clearToken(){
  if(isset($_COOKIE['auth_token'])){
    $token = $_COOKIE['auth_token'];
    $sql = "DELETE FROM auth_token WHERE token='$token'";
    $result = mysqli_query($this->conn, $sql);
  }
}












private function resetToken($user){

    $sql = "DELETE FROM auth_token WHERE user='$user'";
    $result = mysqli_query($this->conn, $sql);
}













private function createResetCode($name, $code){
  $expiry = date('Y-m-d h:i', strtotime('+2 days'));
  $sql = "INSERT INTO reset_code (user, code, expiry) VALUES ('$name', '$code', '$expiry')";

  mysqli_query($this->conn, $sql);
}











private function resetResetCode($name, $code){
  $sql = "DELETE FROM reset_code WHERE user='$name'";
  mysqli_query($this->conn, $sql);
}









private function createProfile($user, $points){
  $image = 'default.png';
  $sql = "INSERT INTO profile (name, point, description, home, relation, photo) VALUES ('$user', '$points', '', '', '', '$image')";
  mysqli_query($this->conn, $sql);
}




}





?>
