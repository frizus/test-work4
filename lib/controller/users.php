<?php
namespace Frizus\Jwt\Controller;

use Bitrix\Main\EO_User;
use Bitrix\Main\Error;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\UserTable;
use Frizus\Jwt\ActionFilter\JwtAuthentication;

class Users extends ControllerWithJwt
{
    protected static $userFields = [
        'ID',
        'LOGIN',
        //'PASSWORD',
        'EMAIL',
        'ACTIVE',
        'BLOCKED',
        'DATE_REGISTER',
        'LAST_LOGIN',
        'LAST_ACTIVITY_DATE',
        'TIMESTAMP_X',
        'SECOND_NAME',
        'LAST_NAME',
        'TITLE',
        'EXTERNAL_AUTH_ID',
        'XML_ID',
        'BX_USER_ID',
        'CONFIRM_CODE',
        'LID',
        'LANGUAGE_ID',
        'TIME_ZONE',
        'TIME_ZONE_OFFSET',
        'PERSONAL_PROFESSION',
        'PERSONAL_PHONE',
        'PERSONAL_MOBILE',
        'PERSONAL_WWW',
        'PERSONAL_ICQ',
        'PERSONAL_FAX',
        'PERSONAL_PAGER',
        'PERSONAL_STREET',
        'PERSONAL_MAILBOX',
        'PERSONAL_CITY',
        'PERSONAL_STATE',
        'PERSONAL_ZIP',
        'PERSONAL_COUNTRY',
        'PERSONAL_BIRTHDAY',
        'PERSONAL_GENDER',
        'PERSONAL_PHOTO',
        'PERSONAL_NOTES',
        'WORK_COMPANY',
        'WORK_DEPARTMENT',
        'WORK_PHONE',
        'WORK_POSITION',
        'WORK_WWW',
        'WORK_FAX',
        'WORK_PAGER',
        'WORK_STREET',
        'WORK_MAILBOX',
        'WORK_CITY',
        'WORK_STATE',
        'WORK_ZIP',
        'WORK_COUNTRY',
        'WORK_PROFILE',
        'WORK_LOGO',
        'WORK_NOTES',
        'ADMIN_NOTES',
    ];

    protected static $userGroupFields = [
        'GROUP_ID',
        'DATE_ACTIVE_FROM',
        'DATE_ACTIVE_TO',
    ];

    protected static $groupFields = [
        'ACTIVE',
        'NAME',
        'STRING_ID',
    ];

	public function indexAction()
	{
        $nav = new PageNavigation('page');
        $nav->initFromUri();
        $nav->setPageSize(10)
            ->allowAllRecords(false);
        $cacheTime = 1;//60 * 60 * 24 * 7;
        $result = UserTable::getList([
            'select' => static::$userFields,
            'order' => ['ID' => 'ASC'],
            'offset' => $nav->getOffset(),
            'limit' => $nav->getLimit(),
            'cache' => [
                'ttl' => $cacheTime,
            ]
        ]);

        $data = [
            'count' => $result->getSelectedRowsCount(),
            'page' => $nav->getCurrentPage(),
            'items' => [],
        ];

        $ids = [];

        while ($row = $result->fetchObject()) {
            $data['items'][$row['ID']] = $this->getUserDatum($row);
            $data['items'][$row['ID']]['GROUPS'] = [];
            $ids[] = $row['ID'];
        }

        if (!empty($ids)) {
            $result = UserGroupTable::getList([
                'select' => array_merge(static::$userGroupFields, ['GROUP']),
                'filter' => [
                    '@USER_ID' => $ids,
                ],
                'cache' => [
                    'ttl' => $cacheTime,
                    'cache_joins' => true,
                ]
            ]);

            while ($row = $result->fetchObject()) {
                foreach (static::$userGroupFields as $field) {
                    $data['items'][$row['USER_ID']]['GROUPS'][$row['GROUP_ID']][$field] = $row[$field];
                }
                foreach (static::$groupFields as $field) {
                    $data['items'][$row['USER_ID']]['GROUPS'][$row['GROUP_ID']][$field] = $row['GROUP'][$field];
                }
            }
        }

        return $data;
	}

	public function showAction($id)
	{
        $result = UserTable::getList([
            'select' => array_merge(static::$userFields),
            'filter' => [
                '=ID' => $id,
            ],
        ]);

        $row = $result->fetchObject();

        if (!isset($row)) {
            $this->addError(new Error('Не найден пользователь', 'user_not_found'));
            return;
        }

        $user = $this->getUserDatum($row);
        $user['GROUPS'] = [];

        $result = UserGroupTable::getList([
            'select' => array_merge(static::$userGroupFields, ['GROUP']),
            'filter' => [
                '=USER_ID' => $row['ID'],
            ]
        ]);

        while ($row = $result->fetchObject()) {
            foreach (static::$userGroupFields as $field) {
                $user['GROUPS'][$row['GROUP_ID']][$field] = $row[$field];
            }
            foreach (static::$groupFields as $field) {
                $user['GROUPS'][$row['GROUP_ID']][$field] = $row['GROUP'][$field];
            }
        }

        return $user;
	}

    protected function getUserDatum(EO_User $row)
    {
        $datum = [];
        foreach (static::$userFields as $field) {
            $datum[$field] = $row[$field];
        }
        return $datum;
    }

	protected function getDefaultPreFilters()
    {
        return [
            new JwtAuthentication()
        ];
    }
}