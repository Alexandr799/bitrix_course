<?php

use Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loader::includeModule('iBlock');

$blackCount = CIBlockElement::GetList(["SORT" => "ASC"], [
    'IBLOCK_ID' => 6,
    'PROPERTY_COLOR_VALUE' => 'Черный'
])->SelectedRowsCount();

$whiteCount = CIBlockElement::GetList(["SORT" => "ASC"], [
    'IBLOCK_ID' => 6,
    'PROPERTY_COLOR_VALUE' => 'Белый'
])->SelectedRowsCount();

$arResult['PRODUCT'] = [
    'black' => $blackCount,
    'white' => $whiteCount
];
