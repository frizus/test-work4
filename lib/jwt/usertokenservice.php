<?php

namespace Frizus\Jwt\JWT;

use Frizus\Jwt\JWT\Algorithm\BaseAlgorithm;

class UserTokenService
{
    private $algorithm;

    private $payload;

    public function __construct(BaseAlgorithm $algorithm, Payload $payload) {
        $this->algorithm = $algorithm;
        $this->payload = $payload;
    }

    public function createToken($uid)
    {
        return $this->algorithm->create($uid, $this->payload);
    }
}
