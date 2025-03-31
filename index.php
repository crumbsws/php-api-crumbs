<?php 
require_once './utils/router.php';
require_once './controllers/auth/authController.php';

$router = new Router();

$router->add('POST', '/auth/verify', function() {
    $authController = new AuthController();
    $authController->authenticate();
});

$router->add('POST', '/auth/login', function() {
    $authController = new AuthController();
    $authController->login(); // for login fix the response structure -
});

$router->add('POST', '/auth/register', function() {
    $authController = new AuthController();
    $authController->register(); // for login fix the response structure -
});

$router->add('POST', '/auth/logout', function() {
    $authController = new AuthController();
    $authController->register(); // for login fix the response structure -
});

$router->add('POST', '/auth/reset', function() {
    $authController = new AuthController();
    $authController->resetPassword(); // for login fix the response structure -
});

$router->dispatch();
?>