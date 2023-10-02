<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <? $APPLICATION->ShowHead() ?>
    <title><? $APPLICATION->ShowTitle() ?></title>
</head>
<?php include_once($_SERVER['DOCUMENT_ROOT']  . '/local/templates/.default/include/header.php') ?>
<h2>это шаблон для ГЛАВНОЙ</h2>
