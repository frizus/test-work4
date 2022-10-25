<?php

namespace Frizus\Jwt\JWT\Algorithm;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Exception;

abstract class BaseAlgorithm
{
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $publicKey = '';

    /**
     * @param string|null $privateKey
     * @param string|null $publicKey
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws Exception
     */
    public function __construct(?string $privateKey = null, ?string $publicKey = null)
    {
        $this->key = $privateKey ?: (string)Option::get('frizus.jwt', 'JWT_SECRET');
        if (empty($this->key)) {
            throw new Exception('JWT private key is empty');
        }

        $this->publicKey = $publicKey ?: (string)Option::get('frizus.jwt', 'JWT_PUBLIC_KEY');
    }

    protected function getKey(): string
    {
        return $this->key;
    }

    protected function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
