<?php
function createProfile($conn, $user, $points){
    $image = 'default.png';
    $sql = "INSERT INTO profile (name, point, description, home, relation, photo) VALUES ('$user', '$points', '', '', '', '$image')";
    mysqli_query($conn, $sql);
  }
  function addPoint($conn, $user, $amount){
    $sql = "UPDATE profile SET point = point + '$amount' WHERE name='$user'";
    mysqli_query($conn, $sql);
  } 
  function getClub($conn, $user){
    $sql = "SELECT * FROM clubs WHERE name IN(SELECT club FROM club_user WHERE user='$user')";
    $result = mysqli_query($conn, $sql);
    $data = array();
    while($row = mysqli_fetch_array($result)) {
      $data[] = $row;
    }
    return $data;
  }
  function getOwnedClub($conn, $user){
    $sql = "SELECT * FROM clubs WHERE founder='$user'";
    $result = mysqli_query($conn, $sql);
    $data = array();
    while($row = mysqli_fetch_array($result)) {
      $data[] = $row;
    }
    return $data;
  }

  function getOtherClub($conn, $name){
    $sql = "SELECT * FROM clubs WHERE name='$name'";
    $result = mysqli_query($conn, $sql);
    $data = array();
    while($row = mysqli_fetch_array($result)) {
      $data[] = $row;
    }
    return $data;
  }

  function setClub($conn, $user, $club){
    $sql = "INSERT IGNORE INTO club_user (user, club) VALUES ('$user', '$club')";
    mysqli_query($conn, $sql);
  }
  function leaveClub($conn, $user, $club){
    $sql = "SELECT * FROM club_user WHERE club='$club' AND user='$user'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) === 1) {
      $sql = "DELETE FROM clubs WHERE name='$club'";
      mysqli_query($conn, $sql);
    }
    $sql = "DELETE FROM club_user WHERE user='$user' AND club='$club'";
    mysqli_query($conn, $sql);
  }
  function createClub($conn, $name, $founder, $description, $card, $point){
    $image = 'default.png';
    $sql = "INSERT INTO clubs (name, founder, description, card, point, photo) VALUES ('$name', '$founder', '$description', '$card','$point', '$image')";
    //points will be set to the points of the user
    mysqli_query($conn, $sql);
  }

  function getRequests($conn, $user){
    $sql = "SELECT * FROM requests WHERE receiver='$user' OR sender='$user' ORDER BY date DESC";
    $result = mysqli_query($conn, $sql);
    $data = array();
    while($row = mysqli_fetch_array($result)) {
      $data[] = $row;
    }
    return $data;
  }
  function getSentRequests($conn, $user){
    $sql = "SELECT receiver FROM requests WHERE sender='$user' ORDER BY date DESC";
    $result = mysqli_query($conn, $sql);
    $data = array();
    while($row = mysqli_fetch_array($result)) {
      $data[] = $row;
    }
    return $data;
  }
  function getUnseenRequests($conn, $user){
    $sql = "SELECT * FROM requests WHERE status='unseen' AND receiver='$user'";
    $result = mysqli_query($conn, $sql);
    $data = array();
    while($row = mysqli_fetch_array($result)) {
      $data[] = $row;
    }
    return $data;
  }
  function updateRequests($conn, $user){
    $sql = "UPDATE requests SET status='pending' WHERE status='unseen' AND receiver='$user'";
    mysqli_query($conn, $sql);
  }
  function checkFriends($conn, $friend_1, $friend_2){  
  $sql = "SELECT * FROM friends WHERE (user_1='$friend_1' OR user_2='$friend_1') AND (user_1='$friend_2' OR user_2='$friend_2')";
  $result = mysqli_query($conn, $sql);
  if(mysqli_num_rows($result) === 0) {
    return false;
  }
  else {
   return true;
  }
}
function getContacts($conn, $user){  
  $sql = "SELECT url FROM channel_user WHERE user='$user'";
  $result = mysqli_query($conn, $sql);
  $data = array();
  while($row = mysqli_fetch_array($result)) {
    $data[] = $row;
  }
  return $data;
}


function createToken($conn, $name){
  $token = bin2hex(random_bytes(16));
  $expiry = date('Y-m-d h:i', strtotime('+30 days'));
  $sql = "INSERT INTO auth_token (user, token, expiry) VALUES ('$name', '$token', '$expiry')";
  setcookie('auth_token', $token, time() + (30 * 24 * 60 * 60), "/", "", true, true);

  mysqli_query($conn, $sql);
}

function checkToken($conn){
  if(isset($_COOKIE['auth_token'])){
    $token = $_COOKIE['auth_token'];
    $date = date('Y-m-d h:i');
    $sql = "SELECT user FROM auth_token WHERE token='$token' AND expiry > '$date'";
    $result = mysqli_query($conn, $sql);
  while($row = mysqli_fetch_array($result)) {
    return $row['user'];
    
  }}
  
  return null;

}

function clearToken($conn){
  if(isset($_COOKIE['auth_token'])){
    $token = $_COOKIE['auth_token'];
    $sql = "DELETE FROM auth_token WHERE token='$token'";
    $result = mysqli_query($conn, $sql);
  }
}

function resetToken($conn, $user){

    $sql = "DELETE FROM auth_token WHERE user='$user'";
    $result = mysqli_query($conn, $sql);
}

function createResetCode($conn, $name, $code){
  $expiry = date('Y-m-d h:i', strtotime('+2 days'));
  $sql = "INSERT INTO reset_code (user, code, expiry) VALUES ('$name', '$code', '$expiry')";

  mysqli_query($conn, $sql);
}
function resetResetCode($conn, $name, $code){
  $sql = "DELETE FROM reset_code WHERE user='$name'";
  mysqli_query($conn, $sql);
}


function getSystemMessages($conn, $user){
  $sql = "SELECT * FROM system_messages WHERE receiver='$user' ORDER BY date DESC";
  $result = mysqli_query($conn, $sql);
  $data = array();
  while($row = mysqli_fetch_array($result)) {
    $data[] = $row;
  }
  return $data;
}

function getUnseenSystemMessages($conn, $user){
  $sql = "SELECT * FROM system_messages WHERE status='unseen' AND receiver='$user'";
  $result = mysqli_query($conn, $sql);
  $data = array();
  while($row = mysqli_fetch_array($result)) {
    $data[] = $row;
  }
  return $data;
}
function updateSystemMessages($conn, $user){
  $sql = "UPDATE system_messages SET status='seen' WHERE status='unseen' AND receiver='$user'";
  mysqli_query($conn, $sql);
}
function createSystemMessage($conn, $receiver, $message){
  $date = date('Y-m-d h:i');
  mysqli_real_escape_string($conn, $message);
  $sql = "INSERT INTO system_messages (receiver, message, status, date) VALUES ('$receiver', '$message', 'unseen', '$date')";
  mysqli_query($conn, $sql);
}
?>
