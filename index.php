<?php 

session_start();

require_once './utils/Router.php';
require_once './controllers/auth/authController.php';
require_once './controllers/clubs/clubController.php';
require_once './controllers/users/profileController.php';
require_once './controllers/requests/requestsController.php';
require_once './controllers/content/contentController.php';
require_once './controllers/content/publishController.php';
require_once './controllers/messages/messageController.php';
require_once './controllers/misc/searchController.php';




$router = new Router();

$router->add('GET', '/auth/verify', function() {
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
    $authController->logout(); 
});

$router->add('POST', '/auth/reset', function() {
    $authController = new AuthController();
    $authController->resetPassword(); 
});



$router->add('GET', '/profile/get', function() {
    $clubController = new ProfileController();
    $clubController->getProfile(); 
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

$router->add('POST', '/clubs/join', function() {
    $clubController = new ClubController();
    $clubController->joinClub(); 
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


$router->add('POST', '/contents/reactions', function() {
    $requestsController = new ContentController();
    $requestsController->getReactions();
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

$router->add('GET', '/contents/trending', function() {
    $requestsController = new ContentController();
    $requestsController->getTrends(); 
});

$router->add('GET', '/contents/friends', function() {
    $publishController = new ContentController();
    $publishController->getFriends();
});

$router->add('GET', '/contents/gallery', function() {
    $publishController = new ContentController();
    $publishController->getGallery();
});


$router->add('POST', '/publish/post', function() {
    $publishController = new PublishController();
    $publishController->createPost();
});

$router->add('POST', '/publish/pin', function() {
    $publishController = new PublishController();
    $publishController->createPin();
});

$router->add('POST', '/publish/gossip', function() {
    $publishController = new PublishController();
    $publishController->createGossip();
});

$router->add('POST', '/publish/diary', function() {
    $publishController = new PublishController();
    $publishController->createDiary();
});

$router->add('POST', '/publish/reaction', function() {
    $publishController = new PublishController();
    $publishController->createReaction();
});


$router->add('POST', '/messages/send', function() {
    $publishController = new MessageController();
    $publishController->createMessage();
});

$router->add('GET', '/messages/channel', function() {
    $publishController = new MessageController();
    $publishController->getChannel();
});

$router->add('GET', '/messages/get', function() {
    $publishController = new MessageController();
    $publishController->getMessages();
});

$router->add('GET', '/messages/conversations', function() {
    $publishController = new MessageController();
    $publishController->getConversations();
});

$router->add('POST', '/messages/prepare', function() {
    $publishController = new MessageController();
    $publishController->prepareChannel();
});

$router->add('POST', '/messages/upload', function() {
    $publishController = new MessageController();
    $publishController->uploadMessageAssets();
});

$router->add('POST', '/search', function() {

    $searchController = new SearchController();
    $searchController->search();

});

$router->dispatch();
?>