<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;


class AdminListComponent extends CBitrixComponent
{
    public function getAdmins()
    {
        $adminGroupID = 1;

        $result = UserTable::getList([
            'filter' => ['GROUPS.GROUP_ID' => $adminGroupID],
            'select' => ['ID', 'LOGIN', 'EMAIL', 'NAME', 'LAST_NAME'],
        ]);

        return $result->fetchAll();
    }
    public function executeComponent()
    {
        $this->arResult['ADMINS'] = $this->getAdmins();
        $this->includeComponentTemplate();
    }
}
