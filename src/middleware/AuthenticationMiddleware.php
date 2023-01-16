<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '\..\..\app\jwt\JTWValidator.php';

class AuthenticationMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        try {
            JTWValidator::validateJWT($request);
        } catch (\Throwable $e) {
            $response = new Response();
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(401);
        }

        return $response;
    }
}