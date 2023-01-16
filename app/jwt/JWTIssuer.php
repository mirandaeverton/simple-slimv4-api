<?php

use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;

class JWTIssuer
{
    private static Sha256 $algorithm;
    private static InMemory $signingKey;
    private static Builder $tokenBuilder;

    public static function issueJWT($claims)
    {
        self::$algorithm = new Sha256();
        self::$signingKey = InMemory::plainText('ARTSOFT');

        self::$tokenBuilder = new Builder(new JoseEncoder(), ChainedFormatter::default());

        $now = new DateTimeImmutable();
        $token = self::$tokenBuilder
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
}