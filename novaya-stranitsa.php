<?php
/**
 * @var CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("some", "какой то!!!!");
$APPLICATION->SetPageProperty("keywords_inner", "фыв");
$APPLICATION->SetPageProperty("title", "Новая страница проперти");
$APPLICATION->SetPageProperty("keywords", "ключи");
$APPLICATION->SetPageProperty("description", "Новая страница");
$APPLICATION->SetTitle("Новая страница");

$APPLICATION->ShowTitle();
$APPLICATION->ShowProperty('title');
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
