<?php


require_once './initials/conn.php';
require_once './utils/response.php';

class RequestsController
{

    private $response;
    private $conn;
    private $data;



    public function __construct()
    {
        $this->conn = new Connector();
        $this->conn = $this->conn->connect();
        $this->response = new Response();
        $this->data = array_merge(
            $_GET ?? [],
            $_POST ?? [],
            json_decode(file_get_contents('php://input'), true) ?? []
        );
        session_start();
    }



    public function getRequests()
    {
        $user = $_SESSION['user']; 
        

        if(isset($this->data['user']) && $this->data['status'] = 'unseen'){
            $sql = "SELECT * FROM requests WHERE status='unseen' AND receiver='$user'";
            $data = array();
            if($result = mysqli_query($this->conn, $sql)) {
            
                while($row = mysqli_fetch_array($result)) 
                {
                $data[] = $row;
                }
                $message = 'Returned unseen requests';
                $state = 'success';
                $this->response->send($state, $message, ['data' => $data]);
                
            }
            else {
                $message = 'Error in getting requests';
                $state = 'error';
                $this->response->send($state, $message, ['data' => []]);
            }
        }
        else {
            $sql = "UPDATE requests SET status='pending' WHERE status='unseen' AND receiver='$user'";
            if(mysqli_query($this->conn, $sql)) {
                $sql = "SELECT * FROM requests WHERE receiver='$user' OR sender='$user' ORDER BY date DESC";
                $data = array();
                if($result = mysqli_query($this->conn, $sql)) {
            
                while($row = mysqli_fetch_array($result)) {
                $data[] = $row;
                }
                $message = 'Returned requests';
                $state = 'success';
                $this->response->send($state, $message, ['data' => $data]);
                } 
                else {
                    $message = 'Error in getting requests';
                    $state = 'error';
                    $this->response->send($state, $message, ['data' => []]);
                }

            }
            else {
                $message = 'Error in getting requests';
                $state = 'error';
                $this->response->send($state, $message, ['data' => []]);
            }

        }

        
    }











    public function sendRequest()
    {
        $user = $_SESSION['user']; 
        if(isset($this->data['user']))
        {
            $sender = $user;
            $receiver = $this->data['user'];
            $date = date("Y-m-d h:i:sa");
                if($sender !== $receiver) {
            $sql = "SELECT * FROM requests WHERE sender = '$sender' AND receiver = '$receiver'";
        $result = mysqli_query($this->conn, $sql);
        if(mysqli_num_rows($result) === 0) {
        $sql = "INSERT INTO requests (sender, receiver, status, date) VALUES ('$sender', '$receiver', 'unseen', '$date')";
        mysqli_query($this->conn, $sql);
        $message = 'Sent to ' . $receiver;
        $state = 'success';
        $this->response->send($state, $message);
  
        }
        else
        {
          $state = 'error';
          $message = 'Already sent';
        $this->response->send($state, $message);
        }
    }
    else {
        $state = 'error';
        $message = 'You cannot send a request to yourself';
        $this->response->send($state, $message);
    }
}

    }












    public function acceptRequest()
    {
        $user = $_SESSION['user'];
        if(isset($this->data['user']) )
    {
    $sender = $this->data['user'];
    $receiver = $user;
    $date = date("Y-m-d h:i:sa");
    $sql = "SELECT * FROM requests WHERE sender = '$sender' AND receiver = '$receiver'";
    if($result = mysqli_query($this->conn, $sql)) {
    if(mysqli_num_rows($result) > 0) {
      $sql = "INSERT INTO friends (user_1, user_2, level) VALUES ('$sender', '$receiver', 0)";
      if(mysqli_query($this->conn, $sql)){
      $sql = "UPDATE requests SET status='accepted' WHERE sender = '$sender' AND receiver = '$receiver'";
      if(mysqli_query($this->conn, $sql)) {

      $message = 'You are now friends with ' . $receiver;
      //createSystemMessage($this->conn, $sender, $message); revise system messages without sending the message from backend

      $message = 'Accepted ' . $sender;
      $state = 'success';
      $this->response->send($state, $message);

    }
      else {
        $state = 'error';
        $message = 'Error accepting request';
        $this->response->send($state, $message);
      }

    } 
    else {
      $state = 'error';
      $message = 'Error accepting request';
    $this->response->send($state, $message);
    }
  
    }
    else
    {
      $state = 'error';
      $message = 'No request';
      setResponse($state, $message);
    }
} 
    else {
        $state = 'error';
        $message = 'Error accepting request';
        $this->response->send($state, $message);
    }
    }
}
    }


?>