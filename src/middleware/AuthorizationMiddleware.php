<?php
declare(strict_types=1);
namespace Src\Middleware;

use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '\..\..\app\AuthJWT.php';

class AuthorizationMiddleware
{
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $authJWT = new AuthJWT();

        try {
            $authJWT->validateJWTPermissions($request);
        } catch (\Throwable $e) {
            $response = new Response();
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(401);
        }

        return $response;
    }
}