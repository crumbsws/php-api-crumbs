<?php 

require_once './initials/conn.php';
require_once './utils/response.php';

class ProfileController {

  
  private $response;
  private $conn;
  private $data;




  public function __construct()
  {
    $this->conn = new Connector();
    $this->conn = $this->conn->connect();
    $this->response = new Response();
    $this->data = json_decode(file_get_contents('php://input'), true);
    session_start();
  }


  public function getProfile() {

    if(isset($this->data['user']))
{
    $userToFind = $this->data['user'];
    $sql = "SELECT profile.*, diary.message
FROM profile
LEFT JOIN diary ON diary.name = profile.name
WHERE profile.name = '$userToFind'
ORDER BY date DESC
LIMIT 1;";

//get the latest diary message
if($result = mysqli_query($conn, $sql)) {
$data = array();
while($row = mysqli_fetch_array($result)) {
  $data[] = $row;
}
$state = 'success';
$message = 'User details found.';
$this->response->send($state, $message, ['data' => $data]);
}
else {
    $state = 'error';
    $message = 'Query failed.';
    $this->response->send($state, $message);  
}
}

  }




}

?>