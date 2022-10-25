<?php
namespace Frizus\Jwt\JWT;

class TokenContext
{
    protected $token;

    protected $data;

    public function __construct(string $token, array $data)
    {
        $this->token = $token;
        $this->data = $data;
    }

    public function isExpired(): bool
    {
        $unixTime = (int)$this->data['exp'];
        return $unixTime <= time();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getUid()
    {
        return $this->data['uid'];
    }

    public function __toString()
    {
        return $this->token;
    }
}
