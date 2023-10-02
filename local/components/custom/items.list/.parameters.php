<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Loader;

if (!Loader::includeModule("iblock")) {
    return;
}

$iblockList = [];
$sectionList = [];

$dbIblocks = IblockTable::getList([
    'select' => ['ID', 'NAME'],
    'filter' => ['ACTIVE' => 'Y'],
]);

while ($iblock = $dbIblocks->fetch()) {
    $iblockList[$iblock['ID']] = $iblock['NAME'];
}

if (!empty($iblockList)) {
    $dbSections = SectionTable::getList([
        'select' => ['ID', 'NAME', 'IBLOCK_ID'],
        'filter' => ['ACTIVE' => 'Y'],
    ]);
    while ($section = $dbSections->fetch()) {
        $sectionList[$section['IBLOCK_ID']][$section['ID']] = $section['NAME'];
    }
}


$arComponentParameters = [
    "GROUPS" => [],
    "PARAMETERS" => [
        "IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => "ID инфоблока",
            "TYPE" => "LIST",
            "VALUES" => $iblockList,
            "REFRESH" => "Y",
            "DEFAULT" => "",
            "MULTIPLE" => "N",
            "REQUIRED" => "Y"
        ],
        "SECTION_ID" => [
            "PARENT" => "BASE",
            "NAME" => "ID раздела",
            "TYPE" => "LIST",
            "VALUES" => $sectionList[$arCurrentValues['IBLOCK_ID']],
            "DEFAULT" => "",
            "MULTIPLE" => "N",
            "REQUIRED" => "Y"
        ]
    ],
];
