<?php
namespace Frizus\Jwt;

use Bitrix\Main\Config\Option;
use Frizus\Jwt\JWT\Algorithm\HS256TokenAlgorithm;
use Frizus\Jwt\JWT\Algorithm\RS256TokenAlgorithm;
use Frizus\Jwt\JWT\Payload;
use Frizus\Jwt\JWT\TokenContext;
use Frizus\Jwt\JWT\UserTokenService;

class JwtToken
{
    public static function createToken($userId, $algorithm = 'HS256')
    {
        $ttl = (int)Option::get('frizus.jwt', 'JWT_TTL', 86400);

        $userTokenService = new UserTokenService(
            $algorithm === 'RS256' ? new RS256TokenAlgorithm() : new HS256TokenAlgorithm(),
            new Payload($ttl)
        );

        return $userTokenService->createToken($userId);
    }

    public static function isValid($token)
    {
        $parts = explode('.', $token, 2);
        $header = base64_decode($parts[0]);

        $algorithm = $header['alg'] === 'RS256' ? new RS256TokenAlgorithm() : new HS256TokenAlgorithm();
        /** @var TokenContext $tokenContext */
        try {
            $tokenContext = $algorithm->read($token);
        } catch (\UnexpectedValueException $e) {
            return false;
        }

        return true;
    }
}