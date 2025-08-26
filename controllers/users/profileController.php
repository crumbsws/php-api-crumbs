<?php

require_once './utils/DBConnector.php';
require_once './utils/Response.php';
require_once './controllers/AppController.php';

class ProfileController extends AppController
{




  public function getProfile()
  {

    if (isset($this->data['user'])) {
      $userToFind = $this->data['user'];
      $sql = "SELECT profile.*, diary.message
FROM profile
LEFT JOIN diary ON diary.name = profile.name
WHERE profile.name = '$userToFind'
ORDER BY date DESC
LIMIT 1;";
      $result = mysqli_query($this->conn, $sql);
      //get the latest diary message
      if ($result) {
        $data = array();
        while ($row = mysqli_fetch_array($result)) {
          $data[] = $row;
        }
        $state = 'success';
        $message = 'User details found.';
        $this->response->send($state, $message, ['data' => $data]);
      } else {
        $state = 'error';
        $message = 'Query failed.';
        $this->response->send($state, $message);
      }
    }

  }

  public function updateProfile()
  {
    print ('updateProfile');


  }

  public function getClubs($findClubOf)
  {
    $sql = "SELECT * FROM clubs WHERE name IN(SELECT club FROM club_user WHERE user='$findClubOf')";
    $result = mysqli_query($this->conn, $sql);
        $clubs = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $clubs[] = $row;
            }
        }
        return $clubs;
    
  }


  public function getContacts($findContactsOf)
  {
    $sql = "SELECT url FROM channel_user WHERE user='$findContactsOf'";
    $result = mysqli_query($this->conn, $sql);
        $clubs = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $clubs[] = $row;
            }
        }
        return $clubs;
  }
}
?>