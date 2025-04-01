<?php 
require_once './utils/router.php';
require_once './controllers/auth/authController.php';
require_once './controllers/clubs/clubController.php';

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



$router->add('GET', '/clubs/get', function() {
    $clubController = new ClubController();
    $clubController->getClub(); // for login fix the response structure -
});

$router->add('POST', '/clubs/create', function() {
    $clubController = new ClubController();
    $clubController->createClub(); // for login fix the response structure -
});

$router->add('POST', '/clubs/update', function() {
    $clubController = new ClubController();
    $clubController->updateClub(); // for login fix the response structure -
});

$router->add('POST', '/clubs/leave', function() {
    $clubController = new ClubController();
    $clubController->leaveClub(); // for login fix the response structure -
});

$router->add('GET', '/clubs/members/get', function() {
    $clubController = new ClubController();
    $clubController->getClubMembers(); // for login fix the response structure -
});

$router->add('POST', '/clubs/members/ban', function() {
    $clubController = new ClubController();
    $clubController->banClubMembers(); // for login fix the response structure -
});


$router->dispatch();
?>