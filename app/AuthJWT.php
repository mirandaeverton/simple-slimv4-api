<?php

declare(strict_types=1);

use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;

require __DIR__ . '\..\vendor\autoload.php';

class AuthJWT{
    private Builder $tokenBuilder;
    private $algorithm;
    private InMemory $signingKey;

    public function issueJWT($userName, $claim) {
        $this->$tokenBuilder = new Builder(new JoseEncoder(), ChainedFormatter::default());
        $this->algorithm = new Sha256();
        $this->signingKey = InMemory::plainText('ARTSOFT');

        $now = new DateTimeImmutable();
        $token = $this->$tokenBuilder
            ->identifiedBy($userName)
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify('+1 hour'))
            // Configures a new claim, called "isAdmin"
            ->withClaim('isAdmin', $claim)
            // Builds a new token
            ->getToken($this->algorithm, $this->signingKey);
        return $token->toString();
    }
}