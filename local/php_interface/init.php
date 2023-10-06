<?php

/**
 * @var CMain $APPLICATION
 */

use Bitrix\Main\EventManager;

require_once __DIR__ . '/vendor/autoload.php';
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/custom/form/models/UsersFromForm.php");
require_once __DIR__ . '/my_tools.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
// var_dump(1);
EventManager::getInstance()->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", function (&$arFields) {
    /**
     * @var CMain $APPLICATION
     */
    global $APPLICATION;

    if ($arFields['ACTIVE'] === 'Y') return;
    if ($arFields["IBLOCK_ID"] !== (int)$_ENV['NEWS_IBLOCK_ID']) return;

    $data = CIBlockElement::GetByID($arFields['ID'])->Fetch();
    if (!$data) return;

    $currentDate = new \Bitrix\Main\Type\DateTime();
    $creationDate = \Bitrix\Main\Type\DateTime::createFromTimestamp(
        MakeTimeStamp($data["DATE_ACTIVE_FROM"])
    );
    $daysDifference = $currentDate->getTimestamp() - $creationDate->getTimestamp();
    $daysDifference = floor($daysDifference / (60 * 60 * 24));

    if ($daysDifference > 3) return;
    $APPLICATION->ThrowException("Вы деактивировали свежую новость");
    return false;
});


EventManager::getInstance()->addEventHandler("iblock", "OnBeforeIBlockElementDelete", function ($ID) {
    /**
     * @var CMain $APPLICATION
     * @var CDatabase $DB
     */
    global $APPLICATION, $DB;
    // $DB->StartTransaction();
    $data = CIBlockElement::GetByID($ID)->Fetch();

    if (!$data) {
        return;
    }

    if ((int)$data["IBLOCK_ID"] !== (int)$_ENV['PRODUCT_IBLOCK_ID']) {
        return;
    }
    // $count = (int)$data['SHOW_COUNTER'];
    // if ($count <= 1) {
    //     $DB->Rollback();
    //     return;
    // }

    // $DB->Rollback();
    // $DB->Rollback();
    // $DB->Commit();
    (new CIBlockElement())->Update($ID, ["ACTIVE" => 'N']);
    // $DB->Rollback();
    $APPLICATION->ThrowException("Нельзя удалить запись! Запись деактивирована!");
    // $DB->Commit();
    return false;
});


EventManager::getInstance()->addEventHandler("main", "OnAfterUserUpdate", function (&$arFields) {
    /**
     * @var CUser $USER
     */
    $contentGroupID = (int)$_ENV['CONTENT_GROUP_ID'];
    $groupsUpdatedIds = array_map(fn ($val) => (int)$val["GROUP_ID"], $arFields["GROUP_ID"]);
    if (!in_array($contentGroupID, $groupsUpdatedIds)) return;

    $userGroupIds = CUser::GetUserGroup($arFields['ID']);
    if (!is_array($userGroupIds)) return;
    if (!in_array(strval($contentGroupID), $userGroupIds)) return;

    $user = CUser::GetByID($arFields['ID'])->Fetch();
    if (!$user) return;


    $email = $user["EMAIL"];
    $login = $user["LOGIN"];
    CEvent::Send('NEW_CONTENT_MANAGER', "s1", [
        "EMAIL" => $email,
        "LOGIN" => $login,
    ]);

    error_log("Send mail on  $email for user $login");
});
