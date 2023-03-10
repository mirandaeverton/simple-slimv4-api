<?php

declare (strict_types = 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

require_once __DIR__ . '\..\src\middleware\AuthenticationMiddleware.php';
require_once __DIR__ . '\..\src\middleware\AuthorizationMiddleware.php';


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
        })->add(AuthorizationMiddleware::class);

        $group->put('/{id}', function (Request $request, Response $response, $args) {
            $postController = new PostController($request, $response, $args);
            return $postController->update();
        })->add(AuthorizationMiddleware::class);

        $group->delete('/{id}', function (Request $request, Response $response, $args) {
            $postController = new PostController($request, $response, $args);
            return $postController->delete($id);
        })->add(AuthorizationMiddleware::class);
    })->add(AuthenticationMiddleware::class);

    // '/user' routes
    $app->group('/user', function (Group $group) {
        require __DIR__ . '/../src/api/user/UserController.php';

        $group->get('', function (Request $request, Response $response) {
            $userController = new UserController($request, $response);
            return $userController->getAll();
        });

        // In Slim v3 the args passed in the URI are not designed by ":" but placed inside {}
        $group->get('/{id}', function (Request $request, Response $response, $args) {
            $userController = new UserController($request, $response, $args);
            return $userController->getSingle();
        });

        $group->post('', function (Request $request, Response $response) {
            $userController = new UserController($request, $response);
            return $userController->create();
        })->add(AuthorizationMiddleware::class);

        $group->put('/{id}', function (Request $request, Response $response, $args) {
            $userController = new UserController($request, $response, $args);
            return $userController->update();
        })->add(AuthorizationMiddleware::class);

        $group->delete('/{id}', function (Request $request, Response $response, $args) {
            $userController = new UserController($request, $response, $args);
            return $userController->delete($id);
        })->add(AuthorizationMiddleware::class);
    })->add(AuthenticationMiddleware::class);

    $app->post('/login', function (Request $request, Response $response) {
        require __DIR__ . '/../src/api/login/LoginController.php';
        $loginController = new LoginController($request, $response);
        return $loginController->login();
    });
};
