# Установка
1. Скопировать репозиторий в папку `frizus.jwt` в папку проекта `/local/modules/`
2. Установить модуль [sprint.migration](https://github.com/andreyryabin/sprint.migration)
3. Установить модули `frizus.jwt`, `spint.migration`
4. Установить миграцию `Настройки -> Миграции для разработчиков -> Миграции (cfg)`
5. Добавить в `/bitrix/.settings.php`
```php
  'routing' => [
      'value' => [
          'config' => ['api.php'],
      ],
  ],
```
6. Заменить в .htaccess
```apache
RewriteCond %{REQUEST_FILENAME} !/bitrix/urlrewrite.php$
RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]
```
на
```apache
RewriteCond %{REQUEST_FILENAME} !/bitrix/routing_index.php$
RewriteRule ^(.*)$ /bitrix/routing_index.php [L]
```
7. Создать файл `/local/routes/api.php` с содержимым:
```php
<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Frizus\Jwt\Controller\Auth;
use Frizus\Jwt\Controller\Register;
use Frizus\Jwt\Controller\Users;

return function (RoutingConfigurator $routes) {
    $routes->post('/local/rest/register', [Register::class, 'register']);
    $routes->post('/local/rest/auth', [Auth::class, 'auth']);
    $routes->get('/local/rest/users/{id}', [Users::class, 'show']);
    $routes->get('/local/rest/users', [Users::class, 'index']);
};
```
# Использование
1. Доступны страницы:
   1. `POST /local/rest/register` принимает параметры `last_name`, `name`, `email`, `phone`, `password`
   2. `POST /local/rest/auth` принимает параметры `login`, `password`
   3. `GET /local/rest/users` принимает параметр `page`, например `/local/rest/users/?page=page-2`
   4. `GET /local/rest/users/<id пользователя>`, например `/local/rest/users/1`
2. Страницы `users` требуют отправки заголовка `Authorization: Bearer <jwt token>`. JWT токен который можно получить на `POST` страницах
  

2. В настройках модуля можно изменить время жизни JWT токена (по умолчанию `2.5 часа`), `Ключ для подписи jwt` (`HS256` и `RS256`), `Публичный ключ` (для `RS256`)
---

Кеширование списка пользователей не сбрасывается после регистрации новых пользователей, так что поставил короткое время кеширования. Не нашел тегированного кеширования у запросов. Возможно чего-то не знаю.

Регистрация написана через `\CUser::Register()`. В методе происходит автоматическая авторизация пользователя в системе. Если развивать идею, то надо использовать JWT `\Frizus\Jwt\Controller\Users::$userJwt`

Тестировал на последней версии Битрикс.
