<?php


require_once './initials/conn.php';
require_once './utils/response.php';

class AppController {


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
    
}

?>