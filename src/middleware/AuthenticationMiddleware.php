<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '\..\..\app\AuthJWT.php';

class AuthenticationMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $authJWT = new AuthJWT();

        try {
            $authJWT->validateJWT($request);
        } catch (\Throwable $e) {
            $response = new Response();
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(401);
        }

        return $response;
    }
}