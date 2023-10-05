<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/**
 *  @var array $arCurrentValues
 */

use Bitrix\Main\Loader;

if (!Loader::includeModule('iblock')) {
    return;
}
$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);
$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = [];
$iblockFilter = [
    'ACTIVE' => 'Y',
];
if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $iblockFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
}
$rsIBlock = CIBlock::GetList(["SORT" => "ASC"], $iblockFilter);
while ($arr = $rsIBlock->Fetch()) {
    $arIBlock[$arr["ID"]] = "[" . $arr["ID"] . "] " . $arr["NAME"];
}

$arSorts = [
    'ASC' => GetMessage('T_IBLOCK_DESC_ASC'),
    'DESC' => GetMessage('T_IBLOCK_DESC_DESC'),
];
$arSortFields = [
    'ID' => GetMessage('T_IBLOCK_DESC_FID'),
    'NAME' => GetMessage('T_IBLOCK_DESC_FNAME'),
    'ACTIVE_FROM' => GetMessage('T_IBLOCK_DESC_FACT'),
    'SORT' => GetMessage('T_IBLOCK_DESC_FSORT'),
    'TIMESTAMP_X' => GetMessage('T_IBLOCK_DESC_FTSAMP'),
];

$arComponentParameters = [
    'PARAMETERS' => [
        "VARIABLE_ALIASES" => [
            "ELEMENT_ID" => ["NAME" =>  'Идентификатор вакансии'],
            "FORM_ID" => ["NAME" =>  'Идецификатор формы'],
        ],
        "CACHE_TIME"  =>  ["DEFAULT" => 36000000],
        "CACHE_FILTER" => [
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => 'Кешировать при установленном фильтре',
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        "CACHE_GROUPS" => [
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => 'Учитывать права доступа',
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],

        "SEF_MODE" => [
            "vacancies" => [
                "NAME" => 'Стараница списка',
                "DEFAULT" => "",
                "VARIABLES" => [],
            ],
            "vacancy" => [
                "NAME" => 'Страница вакансии',
                "DEFAULT" => "#ELEMENT_ID#/",
                "VARIABLES" => [],
            ],
            "rezume" => [
                "NAME" => 'Форма отлика на вакансию',
                "DEFAULT" => "#ELEMENT_ID#/form/",
                "VARIABLES" => [],
            ],
        ],

        "IBLOCK_TYPE" => [
            "PARENT" => "BASE",
            "NAME" => 'Тип инфоблока',
            "TYPE" => "LIST",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => 'Инфоблок',
            "TYPE" => "LIST",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
            "ADDITIONAL_VALUES" => "Y",
        ],
        "NEWS_COUNT" => [
            "PARENT" => "BASE",
            "NAME" => "Колитчество вакансий на странице",
            "TYPE" => "STRING",
            "DEFAULT" => "1",
        ],
        "SORT_BY1" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => 'Поле для первой сортировки новостей',
            "TYPE" => "LIST",
            "DEFAULT" => "ACTIVE_FROM",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SORT_ORDER1" => [
            "PARENT" => "DATA_SOURCE",
            "NAME" => 'Направление для первой сортировки ',
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ],

    ]
];
