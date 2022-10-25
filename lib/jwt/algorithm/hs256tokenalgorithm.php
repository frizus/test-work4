<?php

namespace Frizus\Jwt\JWT\Algorithm;

use Bitrix\Main\Web\JWT;
use Frizus\Jwt\JWT\Payload;
use Frizus\Jwt\JWT\TokenContext;

class HS256TokenAlgorithm extends BaseAlgorithm
{
    private const ALG = 'HS256';

    public function read(string $token)
    {
        $jwt = JWT::decode($token, $this->getKey(), [static::ALG]);
        return new TokenContext($token, (array)$jwt);
    }

    public function create($uid, Payload $dataPacker)
    {
        $data = $dataPacker->getData($uid);
        $token = JWT::encode($data, $this->getKey(), static::ALG);
        return new TokenContext($token, $data);
    }
}

