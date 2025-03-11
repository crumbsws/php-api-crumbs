<?php
session_start();
include('connector.php');
include('library.php');

session_destroy();
clearToken($conn);
setcookie("auth_token", "", time()-3600);



?>