<?php

use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;

require_once __DIR__ . '\JWTParser.php';

class JTWValidator
{

    private static Sha256 $algorithm;
    private static InMemory $signingKey;
    private static Token $token;
    private static Validator $validator;
    private static SignedWith $signer;


    public static function validateJWT($request)
    {
        self::$algorithm = new Sha256();
        self::$signingKey = InMemory::plainText('ARTSOFT');
        self::$validator = new Validator();

        $tokenString = $request->getCookieParams()['TEST'];
        
        self::$token = JWTParser::parseJWT($tokenString);
        if (! self::$token instanceof Token\Plain) {
            throw new ConstraintViolation('You should pass a plain token!');
        }
        
        /**
         * 1. Verificar se foi assinado com a mesma chave
         */
        self::$signer = new SignedWith(self::$algorithm, self::$signingKey);
        
        if (!self::$validator->validate(self::$token, self::$signer)) {
            throw new ConstraintViolation('Error! Token has an invalid signature!');
        }
        
        /**
         * 2. Verificar se tem expiration date
         */
        if(! self::$token->claims()->get('exp')) {
            throw new ConstraintViolation('Invalid token! It has no expiration date!');
        }
        
        /**
         * 3. Verificar se está dentro da validade
         */
        $now = new DateTimeImmutable();
        if (self::$token->isExpired($now)) {
            throw new ConstraintViolation('Token has expired!');
        }

        return true;
    }
}