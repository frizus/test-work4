<?php
namespace Frizus\Jwt;

use Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\FloatField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\TextField;

/**
 * Class JwtTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> UF_USER_ID double optional
 * <li> UF_JWT_TOKEN text optional
 * </ul>
 **/
class UserJwtTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_user_jwt';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => 'ID'
                ]
            ),
            new FloatField(
                'UF_USER_ID',
                [
                    'title' => 'ID пользователя'
                ]
            ),
            new TextField(
                'UF_JWT_TOKEN',
                [
                    'title' => 'JWT токен'
                ]
            ),
        ];
    }
}