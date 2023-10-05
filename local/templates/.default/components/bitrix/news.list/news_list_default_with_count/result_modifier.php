<?php

use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loader::includeModule('iBlock');

$counter = CIBlockElement::GetList(["SORT" => "ASC"], [
    'IBLOCK_ID' => 6,
], ['PROPERTY_COLOR']);

$res = [];
while ($data = $counter->Fetch()) {
    $res[] = ['NAME' => $data["PROPERTY_COLOR_VALUE"], 'CNT' => $data["CNT"]];
}

$arResult['PRODUCT'] = $res;
