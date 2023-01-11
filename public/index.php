<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/api/post/PostController.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// ROUTES

$app->get('/post', function (Request $request, Response $response) {
    $postController = new PostController($request, $response);
    // $data = $postController->getAll();
    return $postController->getAll();
});

// In Slim v3 the args passed in the URI are not designed by ":" but placed inside {}
$app->get('/post/{id}', function (Request $request, Response $response, $args) {
    $postController = new PostController($request, $response, $args);
    return $postController->getSingle();
});

$app->post('/post', function (Request $request, Response $response) {
    $postController = new PostController($request, $response);
    return $postController->create();
});

$app->put('/post/{id}', function (Request $request, Response $response, $args) {
    $postController = new PostController($request, $response, $args);
    return $postController->update();
});

$app->delete('/post/{id}', function (Request $request, Response $response, $args) {
    $postController = new PostController($request, $response, $args);
    return $postController->delete($id);
});

$app->run();