<?php

require_once './utils/DBConnector.php';
require_once './utils/Response.php';
require_once './controllers/AppController.php';

class MessageController extends AppController
{

    public function createMessage()
    {
        if (!empty($this->data['user']) && !empty($this->data['channel']) && !empty($this->data['message']) && !empty($this->data['url'])) {
            $user = mysqli_real_escape_string($this->conn, $this->data['user']);
            $channel = mysqli_real_escape_string($this->conn, $this->data['channel']);
            $message = mysqli_real_escape_string($this->conn, $this->data['message']);
            $url = mysqli_real_escape_string($this->conn, $this->data['url']);
            $date = date("Y-m-d h:i");
            $amount = 1;
            //addPoint($this->conn, $user, $amount);
            if (isset($this->data['reply'])) {
                $reply = $this->data['reply'];
            } else {
                $reply = null;
            }
            if (is_array($this->data['assets']) && !empty($this->data['assets'][0])) {
                $assets = $this->data['assets'];
                foreach ($assets as $x) {
                    $sql = "INSERT INTO message_assets (parent, asset) VALUES ('$url', '$x')";
                    mysqli_query($this->conn, $sql);
                }
            }



            $sql = "INSERT INTO messages (user, channel, url, message, reply, date, status) VALUES ('$user','$channel', '$url', '$message', '$reply', '$date', 'unseen')";
            if (mysqli_query($this->conn, $sql)) {
                $state = 'success';
                $message = 'Message sent';
                $this->response->send($state, $message);
            } else {
                $state = 'error';
                $message = 'Failed to send message';
                $this->response->send($state, $message);
            }
        }
    }




    public function getChannel()
    {
        if (!empty($this->data['channel'])) {
            $channel = $this->data['channel'];
            $user = $_SESSION['user'];
            $sql = "SELECT * FROM profile WHERE name IN(SELECT user FROM channel_user WHERE user!='$user' AND url IN(SELECT url FROM channels WHERE url='$channel'))";
            $result = mysqli_query($this->conn, $sql);
            if ($result) {
                while ($row = mysqli_fetch_array($result)) {
                    $data[] = $row;
                }
                $state = 'success';
                $message = 'Channel details found.';
                $this->response->send($state, $message, ['data' => $data]);
            } else {
                $state = 'error';
                $message = 'Query failed.';
                $this->response->send($state, $message);
            }

        }
    }


    public function getMessages()
    {


        $user = $_SESSION['user'];
        if (isset($this->data['status']) && $this->data['status'] === 'unseen') {
            $sql = "SELECT * FROM messages WHERE channel IN(SELECT url FROM channel_user WHERE user='$user') AND status='unseen' AND user!='$user'";
            $result = mysqli_query($this->conn, $sql);


            if ($result) {
                while ($row = mysqli_fetch_array($result)) {
                    $data[] = $row;
                }
                $state = 'success';
                $message = 'Unseen messages found.';
                $this->response->send($state, $message, ['data' => $data]);
            } else {
                $state = 'error';
                $message = 'Query failed.';
                $this->response->send($state, $message);
            }
        } else {
            if (isset($this->data['channel'])) {


                $channel = mysqli_real_escape_string($this->conn, $this->data['channel']);
                $sql = "SELECT messages.*, GROUP_CONCAT(message_assets.asset) as asset FROM messages LEFT JOIN message_assets ON messages.url = message_assets.parent WHERE messages.channel='$channel' GROUP BY messages.id, messages.url, messages.channel ORDER BY messages.id ASC";
                $result = mysqli_query($this->conn, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_array($result)) {
                        $data[] = $row;
                    }
                    $state = 'success';
                    $message = 'Messages found.';
                    $this->response->send($state, $message, ['data' => $data]);
                } else {
                    $state = 'error';
                    $message = 'Query failed.';
                    $this->response->send($state, $message);
                }


                //non-essential logic
                $sql = "UPDATE messages SET status='seen' WHERE channel='$channel' AND status='unseen' AND user!='$user'";
                mysqli_query($this->conn, $sql);
                //non-essential logic


            }
        }




    }






    public function getConversations()
    {
        $user = $_SESSION['user'];
        $sql = "SELECT profile.name, profile.photo, channel_user.url AS channel, messages.message, messages.user, messages.date, messages.status FROM channel_user 
            JOIN profile ON profile.name = channel_user.user JOIN messages ON messages.channel = channel_user.url
            WHERE profile.name != '$user'
            AND messages.id = (
            SELECT MAX(messages.id)
            FROM messages
            WHERE messages.channel = channel_user.url AND messages.channel IN(SELECT url FROM channel_user WHERE user='$user')
            )
            
            ORDER BY 
            messages.id DESC;";
        $result = mysqli_query($this->conn, $sql);
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $data[] = $row;
            }
            $state = 'success';
            $message = 'Conversations found.';
            $this->response->send($state, $message, ['data' => $data]);
        } else {
            $state = 'error';
            $message = 'Query failed.';
            $this->response->send($state, $message);
        }
    }





    public function prepareChannel()
    {
        if (!empty($data['user'])) {
            $user1 = $_SESSION['user'];
            $user2 = $this->data['user'];
            $sql = "SELECT * FROM account WHERE user='$user2'";
            $result = mysqli_query($this->conn, $sql);
            if ($result && mysqli_num_rows($result) === 1) {
                $sql = "SELECT * FROM channels WHERE url IN(SELECT url FROM channel_user WHERE user='$user1' AND url IN(SELECT url FROM channel_user WHERE user='$user2'))";
                $result = mysqli_query($this->conn, $sql);
                if ($result && mysqli_num_rows($result) === 1) {
                    $row = mysqli_fetch_assoc($result);
                    $url = $row['url'];
                    $state = 'success';//here
                    $message = 'Channel found.';
                    $this->response->send($state, $message, ['url' => $url]);

                } else if (mysqli_num_rows($result) === 0) {
                    $url = uniqid();
                    $sql = "INSERT INTO channels (url) VALUES ('$url')";
                    if (mysqli_query($this->conn, $sql)) {
                        $sql = "INSERT INTO channel_user (user, url) VALUES  ('$user1', '$url'), ('$user2', '$url')";
                        if (mysqli_query($this->conn, $sql)) {
                            $state = 'success';
                            $message = 'Channel created.';
                            $this->response->send($state, $message, ['url' => $url]);
                        }
                    } else {
                        $state = 'error';
                        $message = 'Failed to create channel.';
                        $this->response->send($state, $message);

                    }
                }
            } else {
                $state = 'error';
                $message = 'User not found.';
                $this->response->send($state, $message);
            }
        }
    }




    public function uploadMessageAssets()
    {
        if (!empty($_FILES['asset']['name'][0])) {
            foreach ($_FILES['asset']['name'] as $key => $name) {


                $directory = $_SERVER["DOCUMENT_ROOT"] . "/message-assets/";
                $newName = basename($name);
                $file = $directory . $newName;
                $filetype = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if ($_FILES["asset"]["size"][$key] < 10485760) {
                    if (!in_array($filetype, ['exe', 'bat', 'rat'])) {



                        if (move_uploaded_file($_FILES["asset"]["tmp_name"][$key], $file)) {

                            $state = 'success';
                            $message = 'File transfer succesful.';//Imp
                            setResponse($state, $message);



                        } else {
                            $state = 'error';
                            $message = 'File transfer failed.';//Imp
                            setResponse($state, $message);

                        }
                    } else {
                        $state = 'error';
                        $message = 'File format is not supported.';//Imp
                        setResponse($state, $message);
                        exit;
                    }
                } else {
                    $state = 'error';
                    $message = 'File should be smaller than 10mb.';//Imp
                    setResponse($state, $message);
                    exit;
                }
            }
        }
    }
}
?>