<?php

use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token;

class JWTParser
{
    private static Parser $parser;
    private static Token $token;

    public static function parseJWT($tokenString)
    {
        self::$parser = new Parser(new JoseEncoder());

        try {
            self::$token = self::$parser->parse($tokenString);
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            echo 'Oh no, an error: ' . $e->getMessage();
        }
        assert(self::$token instanceof Token);

        return self::$token;
    }
}