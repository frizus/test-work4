<?php
namespace Frizus\Jwt\Helper;

use Bitrix\Main\Application;

class RequestHelper
{
    public static function isJson()
    {
        $contentType = Application::getInstance()->getContext()->getRequest()->getHeader('content-type');

        if (isset($contentType)) {
            return strpos($contentType, '/json') !== false ||
                strpos($contentType, '+json') !== false;
        }

        return false;
    }

    public static function getPostSource()
    {
        $request = Application::getInstance()->getContext()->getRequest();

        if (static::isJson()) {
            return $request->getJsonList();
        }

        return $request->getPostList();
    }
}