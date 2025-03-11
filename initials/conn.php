<?php
class Connector {

    protected $dbHost = 'db-mysql-fra1-51403-do-user-17164387-0.f.db.ondigitalocean.com:25060';
    protected $dbUser = 'doadmin';
    protected $dbPass = 'AVNS_4ar3mUrmBSvDyBAiynU';

    public function connect() {
        
        
        header('Content-Type: application/json');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Origin: *'); // change before deployment
        header('Access-Control-Allow-Headers: Content-Type');
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        return mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, 'defaultdb');
    }
}


    

?>