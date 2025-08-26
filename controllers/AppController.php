<?php


require_once './utils/DBConnector.php';
require_once './utils/Response.php';

class AppController {


    protected $response;
    protected $conn;
    protected $data;



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
        
    }
    
}

?>