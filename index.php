<?php 
require_once './utils/router.php';
require_once './controllers/auth/authController.php';
require_once './controllers/clubs/clubController.php';
require_once './controllers/requests/requestsController.php';
require_once './controllers/content/contentController.php';
require_once './controllers/content/publishController.php';

$router = new Router();

$router->add('POST', '/auth/verify', function() {
    $authController = new AuthController();
    $authController->authenticate();
});

$router->add('POST', '/auth/login', function() {
    $authController = new AuthController();
    $authController->login(); 
});

$router->add('POST', '/auth/register', function() {
    $authController = new AuthController();
    $authController->register(); 
});

$router->add('POST', '/auth/logout', function() {
    $authController = new AuthController();
    $authController->register(); 
});

$router->add('POST', '/auth/reset', function() {
    $authController = new AuthController();
    $authController->resetPassword(); 
});



$router->add('GET', '/clubs/get', function() {
    $clubController = new ClubController();
    $clubController->getClub(); 
});

$router->add('POST', '/clubs/create', function() {
    $clubController = new ClubController();
    $clubController->createClub(); 
});

$router->add('POST', '/clubs/update', function() {
    $clubController = new ClubController();
    $clubController->updateClub(); 
});

$router->add('POST', '/clubs/leave', function() {
    $clubController = new ClubController();
    $clubController->leaveClub(); 
});

$router->add('GET', '/clubs/members/get', function() {
    $clubController = new ClubController();
    $clubController->getClubMembers(); 
});

$router->add('POST', '/clubs/members/ban', function() {
    $clubController = new ClubController();
    $clubController->banClubMembers(); 
});



$router->add('GET', '/requests/get', function() {
    $requestsController = new RequestsController();
    $requestsController->getRequests(); 
});

$router->add('POST', '/requests/send', function() {
    $requestsController = new RequestsController();
    $requestsController->sendRequest(); 
});

$router->add('POST', '/requests/accept', function() {
    $requestsController = new RequestsController();
    $requestsController->acceptRequest(); 
});



$router->add('GET', '/contents/posts', function() {
    $requestsController = new ContentController();
    $requestsController->getPosts(); 
});

$router->add('GET', '/contents/pins', function() {
    $requestsController = new ContentController();
    $requestsController->getPins(); 
});

$router->add('GET', '/contents/diary', function() {
    $requestsController = new ContentController();
    $requestsController->getDiary(); 
});

$router->add('GET', '/contents/gossips', function() {
    $requestsController = new ContentController();
    $requestsController->getGossips(); 
});

$router->add('GET', '/contents/trends', function() {
    $requestsController = new ContentController();
    $requestsController->getTrends(); 
});


$router->add('POST', '/publish/post', function() {
    $publishController = new PublishController();
    $publishController->createPost();
});

$router->add('POST', '/publish/pin', function() {
    $publishController = new PublishController();
    $publishController->createPin();
});

$router->add('POST', '/publish/note', function() {
    $publishController = new PublishController();
    $publishController->createNote();
});

$router->add('POST', '/publish/diary', function() {
    $publishController = new PublishController();
    $publishController->createDiary();
});

$router->dispatch();
?>