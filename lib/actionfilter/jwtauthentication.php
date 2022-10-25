<?php
namespace Frizus\Jwt\ActionFilter;

use Bitrix\Main\Context;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Frizus\Jwt\JwtToken;
use Frizus\Jwt\UserJwtTable;

class JwtAuthentication extends Base
{
    const ERROR_INVALID_JWT_AUTHENTICATION = 'invalid_jwt_authentication';

    protected $prefix = 'Bearer';

    public function onBeforeAction(Event $event)
    {
        $header = $this->getAction()->getController()->getRequest()->getServer()->get('REMOTE_USER');

        if (strpos($header, $this->prefix) !== false) {
            $token = substr($header, strlen($this->prefix) + 1);

            $result = UserJwtTable::getList([
                'select' => ['UF_USER_ID', 'UF_JWT_TOKEN'],
                'filter' => [
                    '=UF_JWT_TOKEN' => $token,
                ]
            ]);

            $row = $result->fetchObject();

            if ($row) {
                if (JwtToken::isValid($row['UF_JWT_TOKEN'])) {
                    $this->getAction()->getController()->setUserJwt($result);
                    return null;
                }

                $row->delete();
            }
        }

        Context::getCurrent()->getResponse()->setStatus(401);
        $this->addError(new Error(null, self::ERROR_INVALID_JWT_AUTHENTICATION));

        return new EventResult(EventResult::ERROR, null, null, $this);
    }
}