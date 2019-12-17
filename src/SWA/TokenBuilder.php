<?php

namespace SWA;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;

final class TokenBuilder
{
    private $private_key;
    private $key_id;
    private $issued_at;
    private $expiration;
    private $team_id;
    private $client_id;

    public function setPrivateKey($key)
    {
        $this->private_key = $key;
        return $this;
    }

    // A 10-character key identifier obtained from your developer account.
    public function setKid($kid)
    {
        $this->key_id = $kid;
        return $this;
    }

    // The issuer registered claim key, which has the value of your 10-character Team ID, obtained from your developer account.
    public function setIss($iss)
    {
        $this->team_id = $iss;
        return $this;
    }

    // The issued at registered claim key, the value of which indicates the time at which the token was generated, in terms of the number of seconds since Epoch, in UTC.
    public function setIat($iat)
    {
        $this->issued_at = $iat;
        return $this;
    }

    // The expiration time registered claim key, the value of which must not be greater than 15777000 (6 months in seconds) from the Current Unix Time on the server.
    public function setExp($exp)
    {
        $this->expiration = $exp;
        return $this;
    }

    // The subject registered claim key, the value of which identifies the principal that is the subject of the JWT. Use the same value as client_id as this token is meant for your application.
    public function setSub($sub)
    {
        $this->client_id = $sub;
        return $this;
    }

    public function getClientSecret()
    {
        $issued_at = $this->issued_at ? $this->issued_at : time();
        $expiration = $this->expiration ? $this->expiration : ($issued_at + 3600);

        $signer = new Sha256();
        $token = (new Builder())->setHeader('kid', $this->key_id)
                                ->setIssuer($this->team_id)
                                ->setAudience('https://appleid.apple.com')
                                ->setIssuedAt($issued_at)
                                ->setExpiration($expiration)
                                ->set('sub', $this->client_id)
                                ->sign($signer, $this->private_key)
                                ->getToken();
        return $token;
    }
}
