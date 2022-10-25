<?php
namespace Frizus\Jwt\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\UserTable;
use Frizus\Jwt\Helper\RequestHelper;
use Frizus\Jwt\JwtToken;
use Frizus\Jwt\UserJwtTable;

class Register extends Controller
{
	public function registerAction()
	{
        $inputSource = RequestHelper::getPostSource();
        $lastName = $inputSource->get('last_name');
        $name = $inputSource->get('name');
        $email = $inputSource->get('email');
        $phone = $inputSource->get('phone');
        $password = $inputSource->get('password');

        if (!isset($email) ||
            !is_string($email) ||
            ($email === '') ||
            (trim($email) === '') ||
            (filter_var($email, FILTER_VALIDATE_EMAIL) === false) ||
            !isset($password) ||
            !is_string($password) ||
            ($password === '') ||
            (isset($lastName) && !is_string($lastName)) ||
            (isset($name) && !is_string($name)) ||
            !isset($phone) ||
            !is_string($phone) ||
            ($phone === '')
        ) {
            $this->addError(new Error('Укажите last_name, name, email, phone, password'));
            return;
        }

        $correct = false;
        if (preg_match('#^[\d\(\)\-\+\s\r\n]+$#m', $phone)) {
            $phone = preg_replace('#\(\)\-\+\r\n\s#m', '', $phone);
            if ($phone !== '') {
                $correct = true;
                if (strlen($phone) === 11) {
                    if (strpos($phone, '8') === 0) {
                        $phone = '+7' . substr($phone, 1);
                    } else {
                        $phone = '+' . $phone;
                    }
                }
            }
        }

        if (!$correct) {
            $this->addError(new Error('Некорректный номер телефона'));
            return;
        }

        $result = UserTable::getList([
            'select' => ['ID'],
            'filter' => [
                '=LOGIN' => $email,
            ],
            'limit' => 1,
        ]);

        $row = $result->fetch();

        if ($row) {
            $this->addError(new Error('Пользователь с таким email уже создан'));
            return;
        }

        $user = new \CUser();
        $resultMessage = $user->Register($email, $name, $lastName, $password, $password, $email, false, '', 0, false, $phone);

        if ($resultMessage['TYPE'] === 'ERROR') {
            $this->addError(new Error($resultMessage['MESSAGE']));
            return;
        }

        $userId = $user->GetID();
        $user->Logout();

        $token = (string)JwtToken::createToken($userId);

        UserJwtTable::add([
            'UF_USER_ID' => $userId,
            'UF_JWT_TOKEN' => $token,
        ]);

        return [
            'token' => $token
        ];
	}

	protected function getDefaultPreFilters()
    {
        return [

        ];
    }
}