<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Security\Random;

/**
 * Использована реализация https://github.com/beta-eto-code/bx.jwt
 */
class frizus_jwt extends CModule
{
    public function __construct()
    {
        $arModuleVersion = null;
        include __DIR__ . '/version.php';
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = 'frizus.jwt';
        $this->MODULE_NAME = 'JWT пример';
        $this->MODULE_DESCRIPTION = "Для работы требуется модуль https://github.com/andreyryabin/sprint.migration\nПосле установки требуется установить миграции в Настройки -> Миграции для разработчиков -> Миграции (cfg)";
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = 'frizus';
        $this->PARTNER_URI = '';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        CopyDirFiles(__DIR__ . '/migrations/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/migrations/', true, true);

        if (empty(Option::get($this->MODULE_ID, 'JWT_SECRET'))) {
            Option::set($this->MODULE_ID, 'JWT_SECRET', Random::getString(32, true));
        }

        if (empty(Option::get($this->MODULE_ID, 'JWT_TTL'))) {
            Option::set($this->MODULE_ID, 'JWT_TTL', 9000);
        }
    }

    public function doUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}