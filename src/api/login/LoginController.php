<?php

require_once __DIR__ . '\..\..\..\app\AuthJWT.php';

class LoginController
{
    private $request;
    private $response;
    private $userData;
    private $responseData;
    private $responseStatus;
    private $token;
    private UserController $userController;

    public function __construct($request, $response)
    {
        include_once __DIR__ . '\..\user\UserController.php';

        $this->request = $request;
        $this->response = $response;
        $this->responseStatus = 200;
        $this->userController = new UserController();
    }

    public function login()
    {

        // Get user and pass from the requestBody
        $this->getUserAndPasswordFromRequest();

        // Validate user's name and password against DB
        $this->validateUserNameAndPassword();

        // Generate JWT
        $this->generateJWT();

        // Send user data and json web token to the client
        return $this->makeResponse();

    }

    private function getUserAndPasswordFromRequest()
    {
        $this->userData = $this->request->getParsedBody();

        if (!isset($this->userData['name']) || !isset($this->userData['password'])) {
            $this->responseData = array(
                'message' => 'Please, inform both user\'s name and password!',
            );
            $this->responseStatus = 400;
            return $this->makeResponse($this->responseData, $this->responseStatus);
        }
    }

    private function validateUserNameAndPassword()
    {
        $userName = $this->userData['name'];
        $userPassword = $this->userData['password'];

        $validationResult = $this->userController
            ->getSingleByNameAndPassword($userName, $userPassword);

        $this->responseData = $validationResult['data'];
        $this->responseStatus = $validationResult['status'];

        if($this->responseStatus != 200) {
            return $this->makeResponse();
        }

    }

    private function generateJWT(): void 
    {
        if ($this->responseStatus == 200) {
            $authJWT = new AuthJWT();
            $jwt = $authJWT->issueJWT($this->responseData);
            $this->token = $jwt;
        }
    }

    private function makeResponse()
    {
        setcookie('TEST', $this->token, array('httponly' => true));
        $this->response->getBody()->write(json_encode($this->responseData));
        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($this->responseStatus);
    }
}
