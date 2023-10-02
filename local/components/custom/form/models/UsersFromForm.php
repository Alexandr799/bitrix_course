<?php

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

class UsersFromFormTable extends DataManager
{
    public static function getTableName()
    {
        return 'users_from_form';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new StringField('NAME', [
                'required' => true,
            ]),
            new StringField('LAST_NAME', [
                'required' => true,
            ]),
            new StringField('HAS_ACCESS', [
                'required' => true,
            ]),
            new StringField('PHONE_NUMBER', [
                'required' => true,
            ]),
        ];
    }
}
