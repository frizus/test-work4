<?php

namespace Frizus\Jwt\JWT;

use Frizus\Jwt\JWT\Interfaces\DataPackerInterface;
use Bx\Model\Interfaces\UserServiceInterface;
use Bx\Model\Models\User;
use Bx\Model\Services\UserService;
use Closure;
use Exception;

class Payload
{
    protected $ttl;

    public function __construct($tokenTTL)
    {
        $this->ttl = $tokenTTL;
    }

    public function getData($uid)
    {
        $payload = [];
        $payload['uid'] = $uid;
        $unixTime = time();
        $payload['iat'] = $unixTime;
        $payload['exp'] = $unixTime + $this->ttl;
        return $payload;
    }
}
