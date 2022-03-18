<?php

namespace @@namespace@@\Traits;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Ramsey\Uuid\Uuid;

trait TokenTrait
{
    /**
     * Returns the JWT token object
     *
     * @param string $token
     *
     * @return Token
     */
    protected function parseToken($token)
    {
        return (new Parser())->parse($token);
    }

    /**
     * Returns the default audience for the tokens
     *
     * @return string
     */
    protected function getTokenAudience()
    {
        /** @var string $audience */
        $audience = env('token_audience', 'https://phalconphp.com');

        return $audience;
    }

    /**
     * Returns the time the token is issued at
     *
     * @return \DateTimeImmutable
     * @throws
     */
    protected function getTokenTimeIssuedAt()
    {
        return new \DateTimeImmutable();
    }

    /**
     * Returns the time drift i.e. token will be valid not before
     *
     * @return \DateTimeImmutable
     * @throws
     */
    protected function getTokenTimeNotBefore()
    {
        $now = new \DateTimeImmutable();
        return $now->modify('+' . env('TOKEN_NOT_BEFORE', 0) . 'seconds');
    }

    /**
     * Returns the expiry time for the token
     *
     * @return \DateTimeImmutable
     * @throws
     */
    protected function getTokenTimeExpiration()
    {
        $now = new \DateTimeImmutable();
        return  $now->modify('+' . env('JWT_TIMEOUT', 86400) . ' seconds');
    }

    /**
     * @return Key
     */
    protected function getKey()
    {
        return new Key(env('JWT_KEY', ''));
    }

    /**
     * @return mixed
     */
    protected function getIssuer()
    {
        return env('TOKEN_ISSUER', 'https://phalconphp.com');
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getTokenId()
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * @param $user
     * @return string
     * @throws \Exception
     */
    protected function generateToken($user)
    {
        $signer  = new Sha512();
        $builder = new Builder();
        $token   = $builder
            ->issuedBy($this->getIssuer())
            ->permittedFor($this->getTokenAudience())
            ->identifiedBy($this->getTokenId())
            ->issuedAt($this->getTokenTimeIssuedAt())
            ->canOnlyBeUsedAfter($this->getTokenTimeNotBefore())
            ->expiresAt($this->getTokenTimeExpiration())
            ->withClaim('uid',$user->id)
            ->getToken($signer, $this->getKey());

        return $token->toString();
    }

    /**
     * @param Token $token
     * @return mixed
     */
    protected function validateToken($token)
    {
        $signer  = new Sha512();
        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer($this->getIssuer());
        $data->setAudience($this->getTokenAudience());

        return $token->verify($signer, $this->getKey()) && $token->validate($data);
    }
}