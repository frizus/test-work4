<?php

namespace Frizus\Jwt\JWT\Algorithm;

use Bitrix\Main\Web\JWT;
use Frizus\Jwt\JWT\Payload;
use Frizus\Jwt\JWT\TokenContext;
use Exception;

class RS256TokenAlgorithm extends BaseAlgorithm
{
    private const ALG = 'RS256';

    public function __construct(?string $privateKey = null, ?string $publicKey = null)
    {
        parent::__construct($privateKey, $publicKey);
        if (empty($this->getPublicKey())) {
            throw new Exception('JWT public key is empty');
        }
    }

    public function read($token)
    {
        $jwt = JWT::decode($token, $this->getPublicKey(), [static::ALG]);
        return new TokenContext($token, (array)$jwt);
    }

    public function create($uid, Payload $dataPacker)
    {
        $data = $dataPacker->getData($uid);
        $token = JWT::encode($data, $this->getKey(), static::ALG);
        return new TokenContext($token, $data);
    }
}
