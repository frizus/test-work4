<?php
namespace Frizus\Jwt\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Request;
use Bitrix\Main\Security\Password;
use Bitrix\Main\UserTable;
use Frizus\Jwt\Helper\RequestHelper;
use Frizus\Jwt\JwtToken;
use Frizus\Jwt\UserJwtTable;

class Auth extends Controller
{
    /**
     * @see https://habr.com/ru/post/340146/
     * @see http://gricuk.ru/lp/blog/item/novyy-algoritm-kheshirovaniya-paroley-bitrix/
     * @see \CUser::Update()
     */
	public function authAction()
	{
        $inputSource = RequestHelper::getPostSource();
        $login = $inputSource->get('login');
        $password = $inputSource->get('password');

        if (!isset($login) || !is_string($login) || ($login === '') ||
            !isset($password) || !is_string($password) || ($password === '')
        ) {
            $this->addError(new Error('Логин и/или пароль указан не верно', 'wrong_credentials'));
            return;
        }

        $result = UserTable::getList([
            'select' => ['ID', 'PASSWORD'],
            'filter' => [
                '=LOGIN' => $login,
            ],
            'limit' => 1,
        ]);

        $row = $result->fetch();

        if (!$row) {
            $this->addError(new Error('Логин и/или пароль указан не верно', 'wrong_credentials'));
            return;
        }

        if (!Password::equals($row['PASSWORD'], $password)) {
            $this->addError(new Error('Логин и/или пароль указан не верно', 'wrong_credentials'));
            return;
        }

        $userId = $row['ID'];

        $result = UserJwtTable::getList([
            'select' => ['UF_JWT_TOKEN'],
            'filter' => [
                '=UF_USER_ID' => $row['ID'],
            ],
        ]);

        $row = $result->fetchObject();

        if ($row) {
            $token = $row['UF_JWT_TOKEN'];

            if (JwtToken::isValid($token)) {
                $create = false;
            } else {
                $row->delete();
                $create = true;
            }
        } else {
            $create = true;
        }

        if ($create) {
            $token = (string)JwtToken::createToken($userId);

            UserJwtTable::add([
                'UF_USER_ID' => $userId,
                'UF_JWT_TOKEN' => $token,
            ]);
        }

        return [
            'token' => $token,
        ];
	}

	protected function getDefaultPreFilters()
    {
        return [

        ];
    }
}