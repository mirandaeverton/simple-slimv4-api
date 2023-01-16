<?php

declare (strict_types = 1);

use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Validation\ConstraintViolation;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;

require __DIR__ . '\..\vendor\autoload.php';
require_once __DIR__ . '\..\src\models\User.php';


class AuthJWT
{
    private static Builder $tokenBuilder;
    private static $algorithm;
    private static InMemory  $signingKey;
    private static $validator;

    // public function __construct()
    // {
    //     self::$algorithm = new Sha256();
    //     self::$signingKey = InMemory::plainText('ARTSOFT');
    //     self::$validator = new Validator();
    // }

    public static function issueJWT($claims)
    {
        self::$algorithm = new Sha256();
        self::$signingKey = InMemory::plainText('ARTSOFT');
        self::$validator = new Validator();

        $tokenBuilder = new Builder(new JoseEncoder(), ChainedFormatter::default());

        $now = new DateTimeImmutable();
        $token = $tokenBuilder
        // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
        // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify('+1 hour'))
        // Configures a new claim, called "isAdmin"
            ->withClaim('id', $claims['id'])
        // Builds a new token
            ->getToken(self::$algorithm, self::$signingKey);
        return $token->toString();
    }

    public static function parseJWT($tokenString)
    {
        $parser = new Parser(new JoseEncoder());

        try {
            $token = $parser->parse($tokenString);
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            echo 'Oh no, an error: ' . $e->getMessage();
        }
        assert($token instanceof Token);

        return $token;
    }

    public static function validateJWT($request)
    {
        self::$algorithm = new Sha256();
        self::$signingKey = InMemory::plainText('ARTSOFT');
        self::$validator = new Validator();

        $tokenString = self::getTokenFromCookies($request);
        
        $token = self::parseJWT($tokenString);
        if (!$token instanceof Token\Plain) {
            throw new ConstraintViolation('You should pass a plain token!');
        }
        
        /**
         * 1. Verificar se foi assinado com a mesma chave
         */
        $signer = new SignedWith(self::$algorithm, self::$signingKey);
        
        if (!self::$validator->validate($token, $signer)) {
            throw new ConstraintViolation('Error! Token has an invalid signature!');
        }
        
        /**
         * 2. Verificar se tem expiration date
         */
        if(! $token->claims()->get('exp')) {
            throw new ConstraintViolation('Invalid token! It has no expiration date!');
        }
        
        /**
         * 3. Verificar se está dentro da validade
         */
        $now = new DateTimeImmutable();
        if ($token->isExpired($now)) {
            throw new ConstraintViolation('Token has expired!');
        }

        return true;
    }

    public static function validateJWTPermissions($request) 
    {

        $tokenString = self::getTokenFromCookies($request);

        $token = self::parseJWT($tokenString);
        if (!$token instanceof Token\Plain) {
            throw new ConstraintViolation('You should pass a plain token');
        }
        
        /**
         * 1. Retrive the user permissions from DB
         */
        
         // Retrieve user
        $user = new User();
        $user->id = $token->claims()->get('id');
        $stmt = $user->read_single();

         // Get user permissions
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $userPermissions = $isAdmin;
        }
        
        /**
         * 2. Check if the user has the permission
         */
        if(! $userPermissions == '1') {
            throw new ConstraintViolation('Not Allowed');
        }

        return true;
    }

    public static function getTokenFromCookies($request) {
        return $request->getCookieParams()['TEST'];
    }
}
