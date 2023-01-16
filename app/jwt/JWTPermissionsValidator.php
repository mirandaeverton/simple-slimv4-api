<?php

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\ConstraintViolation;

require_once __DIR__ . '\JWTParser.php';
require_once __DIR__ . '\..\..\src\models\User.php';


class JWTPermissionsValidator
{
    private static Token $token;
    private static User $user;
    private static $userPermissions;

    public static function validateJWTPermissions($request) 
    {

        $tokenString = $request->getCookieParams()['TEST'];

        self::$token = JWTParser::parseJWT($tokenString);
        if (!  self::$token instanceof Token\Plain) {
            throw new ConstraintViolation('You should pass a plain token');
        }
        
        /**
         * 1. Retrive the user permissions from DB
         */
        
         // Retrieve user
        self::$user = new User();
        self::$user->id = $token->claims()->get('id');
        $stmt =  self::$user->read_single();

         // Get user permissions
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            self::$userPermissions = $isAdmin;
        }
        
        /**
         * 2. Check if the user has the permission
         */
        if(!  self::$userPermissions == '1') {
            throw new ConstraintViolation('Not Allowed');
        }

        return true;
    }
}