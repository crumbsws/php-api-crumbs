<?php

require_once './controllers/AppController.php';
require_once './utils/DBConnector.php';
require_once './utils/Response.php';

class ClubController extends AppController
{


    public function joinClub()
    {
        if (!empty($this->data['club'])) {
            $club = $this->data['club'];
            $user = $_SESSION['user'];
            $sql = "SELECT * FROM clubs WHERE name='$club'";
            $result = mysqli_query($this->conn, $sql);
            if (mysqli_num_rows($result) === 1) {
                $sql = "INSERT IGNORE INTO club_user (user, club) VALUES ('$user', '$club')";
                if (mysqli_query($this->conn, $sql)) {
                    $state = 'success';
                    $message = 'Joined the club.';
                    $this->response->send($state, $message);
                } else {
                    $state = 'error';
                    $message = 'Error joining club.';
                    $this->response->send($state, $message);
                }
            } else {
                $state = 'error';
                $message = 'Club not found.';
                $this->response->send($state, $message);
            }
        }
    }
   
    public function getClub()
    {


        if (isset($this->data['user'])) {
            
            $findClubOf = $this->data['user'];
            $sql = "SELECT * FROM clubs WHERE name IN(SELECT club FROM club_user WHERE user='$findClubOf')";
            $data = array();

            if ($result = mysqli_query($this->conn, $sql)) {
          
                while ($row = mysqli_fetch_array($result)) {
                    $data[] = $row;
                }
                $state = 'success';
                $message = 'Clubs found';
                $this->response->send($state, $message, ['data' => $data]);
            }
        } else if (isset($this->data['name'])) {
            error_log('User found in getClub');
            $club = $this->data['name'];
            $sql = "SELECT * FROM clubs WHERE name='$club'";
            $data = array();
            if ($result = mysqli_query($this->conn, $sql)) {
                if (mysqli_num_rows($result) == 0) {
                    $state = 'error';
                    $message = 'No clubs found';
                    $this->response->send($state, $message, []);
                    return;
                }
                while ($row = mysqli_fetch_array($result)) {
                    $data[] = $row;
                }
                $state = 'success';
                $message = 'Clubs found';
                $this->response->send($state, $message, ['data' => $data]);
            }
        } else {
            $state = 'error';
            $message = 'No club found';
            $this->response->send($state, $message, []);
        }
    }





















    public function createClub() //use empty for posts, isset for gets
    {
        if (!empty($this->data['name'])) {
            $name = str_replace(' ', '', $this->data['name']);
            $sql = "SELECT * FROM clubs WHERE name='$name'";
            $result = mysqli_query($this->conn, $sql);
            if (mysqli_num_rows($result) === 0) {
                $founder = $_SESSION['user'];
                $description = 'The club of ' . $founder;
                $card = 'Crumbs';
                $point = 0;
                $image = 'default.png';
                $sql = "INSERT INTO clubs (name, founder, description, card, point, photo) VALUES ('$name', '$founder', '$description', '$card','$point', '$image')";
                if (mysqli_query($this->conn, $sql)) {
                    $sql = "INSERT INTO club_user (user, club) VALUES ('$founder', '$name')";
                    if (mysqli_query($this->conn, $sql)) {
                        $state = 'success';
                        $message = 'Club created successfully';
                        $this->response->send($state, $message, []);
                    } else {
                        $state = 'error';
                        $message = 'Error creating club user';
                        $this->response->send($state, $message, []);
                    }
                } else {
                    $state = 'error';
                    $message = 'Error creating club';
                    $this->response->send($state, $message, []);
                }
            } else {
                $state = 'error';
                $message = 'Club already exists';
                $this->response->send($state, $message, []);
            }
        }
    }

















    public function updateClub() //this code sucks ass, unfuck it sometime
    {
        if (!empty($this->data['club'])) {
            $club = $this->data['club'];
            $user = $_SESSION['user'];
            $sql = "SELECT * FROM clubs WHERE name='$club' AND founder='$user'";
            $result = mysqli_query($this->conn, $sql);
            if (mysqli_num_rows($result) === 1) {

                if (isset($this->data['card'])) {
                    if ($this->data['card'] === 'pumpkin') {
                        $card = 'pumpkin';
                    } else if ($this->data['card'] === 'cardinal') {
                        $card = 'cardinal';
                    } else if ($this->data['card'] === 'night') {
                        $card = 'night';
                    } else if ($this->data['card'] === 'pacific') {
                        $card = 'pacific';
                    } else if ($this->data['card'] === 'green') {
                        $card = 'green';
                    } else {
                        $card = 'crumbs';
                    }
                    $sql = "UPDATE clubs SET card='$card' WHERE founder='$user' AND name='$club'"; //IMPROVE THIS OPTIOAL, BUT HANDLE PARAMETERS IN SEARCH
                    $result = mysqli_query($this->conn, $sql);
                }
                if (isset($this->data['description'])) {

                    $description = mysqli_real_escape_string($this->conn, $this->data['description']);
                    $sql = "UPDATE clubs SET description='$description' WHERE founder='$user' AND name='$club'"; //IMPROVE THIS OPTIOAL, BUT HANDLE PARAMETERS IN SEARCH
                    $result = mysqli_query($this->conn, $sql);
                }


                if (isset($_FILES['photo'])) {

                    $directory = $_SERVER["DOCUMENT_ROOT"] . "/club-images/";
                    $newName = basename($club . '-' . $_FILES["photo"]["name"]);
                    $file = $directory . $newName;
                    $filetype = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if ($_FILES["photo"]["size"] < 1200000) {
                        if (in_array($filetype, ['jpg', 'png', 'jpeg', 'gif', 'webp'])) {

                            //same functionalise too

                            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $file)) {

                                $photo = $newName;
                                $sql = "UPDATE clubs SET photo='$photo' WHERE founder='$user' AND name='$club'";
                                $result = mysqli_query($this->conn, $sql);
                            }
                        } else {
                            $state = 'error';
                            $message = 'File format is not supported.'; //Imp
                            $this->response->send($state, $message, []);
                            exit;
                        }
                    } else {
                        $state = 'error';
                        $message = 'File should be smaller than 1100kb.'; //Imp
                        $this->response->send($state, $message, []);
                        exit;
                    }
                }

                $state = 'success';
                $message = 'Club updated.'; //Imp
                $this->response->send($state, $message, []);
            } else {
                $state = 'error';
                $message = 'You can only edit your clubs.'; //Imp
                $this->response->send($state, $message, []);
            }
        }
    }













    public function leaveClub()
    {
        if (!empty($this->data['club'])) {
            $club = $this->data['club'];
            $user = $_SESSION['user'];
            $sql = "SELECT * FROM club_user WHERE club='$club' AND user='$user'";
            $result = mysqli_query($this->conn, $sql);
            if (mysqli_num_rows($result) === 1) {
                $sql = "DELETE FROM clubs WHERE name='$club'"; //functionalise the delete part so you can use it for the delete endpoint aswell
                if (mysqli_query($this->conn, $sql)) {
                    $sql = "DELETE FROM club_user WHERE club='$club'";
                    if (mysqli_query($this->conn, $sql)) {
                        $state = 'success';
                        $message = 'Club left.';
                        $this->response->send($state, $message);
                    } else {
                        $state = 'error';
                        $message = 'Error deleting club user.';
                        $this->response->send($state, $message);
                    }
                } else {
                    $state = 'error';
                    $message = 'Error deleting club.';
                    $this->response->send($state, $message);
                }
            } else {

                $sql = "DELETE FROM club_user WHERE club='$club' AND user='$user'";
                if (mysqli_query($this->conn, $sql)) {
                    $state = 'success';
                    $message = 'Left the club.';
                    $this->response->send($state, $message);
                } else {
                    $state = 'error';
                    $message = 'Error leaving club.';
                    $this->response->send($state, $message);
                }
            }
        }
    }






    public function getClubMembers()
    {
        if (!empty($this->data['name'])) {
            $club = $this->data['name'];
            $sql = "SELECT profile.* FROM club_user JOIN profile ON club_user.user=profile.name WHERE club='$club'";
            $data = array();
            if ($result = mysqli_query($this->conn, $sql)) {
                while ($row = mysqli_fetch_array($result)) {
                    $data[] = $row;
                }
                $state = 'success';
                $message = 'Members found.';
                $this->response->send($state, $message, ['data' => $data]);
            } else {
                $state = 'error';
                $message = 'Error getting members.';
                $this->response->send($state, $message, []);
            }
        }
    }






    public function banClubMembers()
    {
        if (!empty($this->data['club']) && !empty($this->data['users'])) {
            $club = $this->data['club'];
            $user = $_SESSION['user'];
            $usersToBan = $this->data['users'];
            $sql = "SELECT * FROM clubs WHERE name='$club' AND founder='$user'";
            $result = mysqli_query($this->conn, $sql);
            if(mysqli_num_rows($result) > 0) {
                $count = count($usersToBan);
                $i = 0;
                foreach ($usersToBan as $userToBan) {
                    $sql = "DELETE FROM club_user WHERE club='$club' AND user='$userToBan'";           
                    if (mysqli_query($this->conn, $sql)) {
                        if(++$i === $count) {
                            $state = 'success';
                            $message = 'Users banned ';
                            $this->response->send($state, $message, []);
                        }
  
                    } else {
                        $state = 'error';
                        $message = 'Error banning user: ' . $userToBan;
                        $this->response->send($state, $message, []);
                        exit;
                    }
                }

            } else {
                $state = 'error';
                $message = 'You are not the founder of this club.';
                $this->response->send($state, $message, []);
            }
        }
    }
}
