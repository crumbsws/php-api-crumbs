<?php

require_once './utils/DBConnector.php';
require_once './utils/Response.php';
require_once './controllers/AppController.php';

class SearchController extends AppController
{

    public function search()
    {
        if (isset($this->data['value']) && isset($this->data['type'])) {
            $name = $_SESSION['user'];
            $value = $this->data['value'];
            $type = $this->data['type'];
            if ($type == 'people') {
                $sql = "SELECT * FROM profile WHERE name LIKE '%$value%' ORDER BY point DESC LIMIT 8";
            } else if ($type == 'clubs') {
                $sql = "SELECT * FROM clubs WHERE name LIKE '%$value%' ORDER BY point DESC LIMIT 8";
            } else {
                $sql = "SELECT * FROM paths WHERE (title OR body OR name OR collect OR url LIKE '%$value%' OR body LIKE '%$value%' OR name LIKE '%$value%' OR collect LIKE '%$value%' OR url LIKE '%$value%')  AND (access = 'public' 
                            OR (access = 'friends' AND name IN 
                                (SELECT CASE 
                                        WHEN user_2 = '$name' THEN user_1 
                                        ELSE user_2 
                                        END AS friend 
                                FROM friends 
                                WHERE user_1 = '$name' 
                                OR user_2 = '$name'))) ORDER BY date DESC LIMIT 8";
            }

            $result = mysqli_query($this->conn, $sql);
            $data = array();

            while ($row = mysqli_fetch_array($result)) {
                $data[] = $row;


            }
                $state = 'success';
                $message = 'Results retrieved successfully';
                $this->response->send($state, $message, ['data' => $data]);
        }
    }
}
?>