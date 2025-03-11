<?php 
require_once './router.php';
require_once './controllers/auth/authController.php';

$router = new Router();

$router->add('POST', '/auth/authenticate', function() {
    $authController = new AuthController();
    $authController->authenticate();
});

$router->add('POST', '/auth/login', function() {
    $authController = new AuthController();
    $authController->login(); // for login fix the response structure
});

$router->dispatch();
?>