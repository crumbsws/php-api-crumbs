<?php


require_once './initials/conn.php';
require_once './utils/response.php';
require_once './controllers/AppController.php';

class PublishController extends AppController
{

    


    public function createPost()
    {
        $user = $_SESSION['user'];
        if (isset($this->data['title']) && !empty($this->data['body']) && isset($this->data['collect']) && !empty($user)) {

            $url = uniqid();
            $date = date("Y-m-d h:i");

            $directory = $_SERVER["DOCUMENT_ROOT"] . "/images/";

            if (isset($_FILES['conf'])) {
                $newName = basename($url . '-' . $_FILES["conf"]["name"]);
                $file = $directory . $newName;
                if (move_uploaded_file($_FILES["conf"]["tmp_name"], $file)) {
                    $conf = $newName;
                } else {
                    $state = 'error';
                    setResponse($state, []);
                    exit;
                }
            } else {
                $conf = null;
            }

            if (isset($this->data['access'])) {
                if ($this->data['access'] === 'public' || $this->data['access'] === 'friends') {
                    $access = $this->data['access'];
                } else {
                    $access = 'public';
                }
            } else {
                $access = 'public';
            }


            $title = mysqli_real_escape_string($this->conn, $this->data['title']);
            $collect = mysqli_real_escape_string($this->conn, $this->data['collect']);



            $body = mysqli_real_escape_string($this->conn, $this->data['body']);

            if (!empty($this->data['parent'])) {
                $parent = mysqli_real_escape_string($this->conn, $this->data['parent']);
                $sql = "SELECT name, body, title FROM paths WHERE url='$parent'";
                $result = mysqli_query($this->conn, $sql);
                if (mysqli_num_rows($result) === 1) {
                    $row = mysqli_fetch_assoc($result);
                    $parentName = mysqli_real_escape_string($this->conn, $row['name']);
                    $parentTitle = mysqli_real_escape_string($this->conn, $row['title']);
                    $parentBody = mysqli_real_escape_string($this->conn, $row['body']);
                    if ($parentName !== $user) {
                        if ($parentTitle) {
                            $message = $user . ' replied to your post: ' . $parentTitle;
                        } else {
                            $message = $user . ' replied to your post: ' . $parentBody;
                        }
                        //createSystemMessage($this->conn, $parentName, $message);
                        //create the system message by specifying format in sql with the format = object - type - date // current one sucks ass
                    }
                } else {
                    $parent = 'public';
                }

            } else {
                $parent = 'public';
            }

            $sql = "INSERT INTO paths (name, title, parent, url, body, date, conf, collect, access) VALUES ('$user', '$title', '$parent', '$url', '$body', '$date', '$conf', '$collect', '$access')";

            //addPoint($this->conn, $user, $postPoint); too lazy to write it, gotta make a class for it 
            if (mysqli_query($this->conn, $sql)) {
                $message = 'Post created successfully.';
                $state = 'success';
                $this->response->send($state, $message);

            } else {
                $message = 'Error in creating post.';
                $state = 'error';
                $this->response->send($state, $message);
            }


        } else {
            $state = 'error';
            $message = 'Please fill in all fields.';
            $this->response->send($state, $message);

        }

    }


    public function createPin()
    {
        $user = $_SESSION['user'];
        $date = date("Y-m-d h:i");
        if (!empty($this->data['url']) && !empty($this->data['club']) && !empty($this->data['category']) && !empty($_SESSION['user'])) {
            if ($this->data['type'] === 'post') {
                $club = mysqli_real_escape_string($this->conn, $this->data['club']);
                $type = mysqli_real_escape_string($this->conn, $this->data['type']);
                $url = mysqli_real_escape_string($this->conn, $this->data['url']);
                $sql = "SELECT * FROM club_user WHERE club='$club' AND user='$user'";
                $result = mysqli_query($this->conn, $sql);

                if (isset($this->data['quote'])) {
                    $quote = mysqli_real_escape_string($this->conn, $this->data['quote']);
                } else {
                    $quote = null;
                }
                if ($result) {
                    if (mysqli_num_rows($result) === 1) {
                        $sql = "INSERT INTO pins (name, quote, type, url, club, date) VALUES ('$user', '$quote', '$type', '$url', '$club', '$date')";
                        if (mysqli_query($this->conn, $sql)) {
                            $state = 'success';
                            $message = 'Pin created successfully.';

                            $this->response->send($state, $message, ['url' => $url]);

                        } else {
                            $state = 'error';
                            $message = 'Error in creating pin.';
                            $this->response->send($state, $message);
                        }
                    } else {
                        $state = 'error';
                        $message = 'You are not a member of this club.';
                        $this->response->send($state, $message);
                    }
                } else {
                    $state = 'error';
                    $message = 'Error in creating pin.';
                    $this->response->send($state, $message);
                }
            } else if ($this->data['category'] === 'note') {
                error_log('Not released yet');
                exit;
            }
        }
    }


    public function createDiary()
    {
        if (!empty($this->data['message'])) {
            $user = $_SESSION['user'];
            $amount = 1;
            $date = date("Y-m-d h:i");
            $message = mysqli_real_escape_string($this->conn, $this->data['message']);
            $sql = "DELETE FROM diary WHERE name='$user'";
            if (mysqli_query($this->conn, $sql)) {
                $sql = "INSERT INTO diary (name, message, date) VALUES ('$user', '$message', '$date')";
                if (mysqli_query($this->conn, $sql)) {
                    $state = 'success';
                    $message = 'Diary Updated';
                    $this->response->send($state, $message);
                    //addPoint($this->conn, $user, $amount);
                } else {
                    $state = 'error';
                    $message = 'Failed';

                    $this->response->send($state, $message);
                }
            } else {
                $state = 'error';
                $message = 'Failed';
                $this->response->send($state, $message);
            }

        }
    }

    public function createNote()
    {
        if (!empty($this->data['note']) && !empty($this->data['club'])) {
            $user = $_SESSION['user'];
            $amount = 1;
            $date = date("Y-m-d h:i");
            $note = mysqli_real_escape_string($this->conn, $this->data['note']);
            $club = mysqli_real_escape_string($this->conn, $this->data['club']);
            $sql = "INSERT INTO gossip (name, note, club, date) VALUES ('$user', '$note', '$club', '$date')";
            if (mysqli_query($this->conn, $sql)) {
                $state = 'success';
                $message = 'Note Sent';
                $this->response->send($state, $message);
                //addPoint($this->conn, $user, $amount);
            } else {
                $state = 'error';
                $message = 'Failed';
                $this->response->send($state, $message);
            }

        }
    }

    public function createReaction() 
    {
        if(isset($this->data['id'])){
            $id = $this->data['id'];
            $sql = "SELECT reaction.rating, reaction.name, profile.photo FROM reaction INNER JOIN profile on profile.name=reaction.name WHERE url='$id'";
            $result = mysqli_query($this->conn, $sql);
            $response = array();
            
            while($row = mysqli_fetch_assoc($result)) {
                $this->response->send('success', 'Returned reactions', ['data' => $row]);
            }
        
        }
        else{
            $this->response->send('error', 'No reactions found');
        }    
    }

}
?>