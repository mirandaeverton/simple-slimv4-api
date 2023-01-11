<?php

declare (strict_types = 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    
    // '/post' routes
    $app->group('/post', function (Group $group) {
        require __DIR__ . '/../src/api/post/PostController.php';

        $group->get('', function (Request $request, Response $response) {
            $postController = new PostController($request, $response);
            return $postController->getAll();
        });

        // In Slim v3 the args passed in the URI are not designed by ":" but placed inside {}
        $group->get('/{id}', function (Request $request, Response $response, $args) {
            $postController = new PostController($request, $response, $args);
            return $postController->getSingle();
        });

        $group->post('', function (Request $request, Response $response) {
            $postController = new PostController($request, $response);
            return $postController->create();
        });

        $group->put('/{id}', function (Request $request, Response $response, $args) {
            $postController = new PostController($request, $response, $args);
            return $postController->update();
        });

        $group->delete('/{id}', function (Request $request, Response $response, $args) {
            $postController = new PostController($request, $response, $args);
            return $postController->delete($id);
        });
    });
};
