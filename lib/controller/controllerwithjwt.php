<?php
namespace Frizus\Jwt\Controller;

use Bitrix\Main\Engine\Controller;

class ControllerWithJWT extends Controller
{
    protected $userJwt;

    public function setUserJwt($userJwt)
    {
        $this->userJwt = $userJwt;
    }
}