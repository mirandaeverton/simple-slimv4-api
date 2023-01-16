<?php

require __DIR__ . '\..\vendor\autoload.php';
require_once __DIR__ . '\AuthJWT.php';

class TestToken
{
    private $request;
    private $response;
    private $cookies;
    private $authJWT;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->cookies = $this->request->getCookieParams();
        $this->authJWT = new AuthJWT();
    }

    public function validateToken() {
       $tokenString = $this->getTokenFromCookies();
       
       if(!isset($tokenString)){
           $this->response->getBody()->write("You should pass a string formatted token");
           return $this->response;
        }
        
        $this->authJWT->validateJWT($tokenString);
            
        $this->response->getBody()->write("Valid token!");
        return $this->response;
    }
    
    public function validatePermissions() {
        $tokenString = $this->getTokenFromCookies();

        $this->authJWT->validateJWTPermissions($tokenString);

        $this->response->getBody()->write("Valid permissions!");
        return $this->response;

    }

    private function getTokenFromCookies(): string {
        return $tokenString = $this->cookies['TEST'];
    }
}