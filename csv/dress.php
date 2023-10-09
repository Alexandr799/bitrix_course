<?php
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Context;
use Bitrix\Main\Loader;


/**
 * @var CUser $USER
 */
global $USER;

$fileId = $USER->GetID();
$fileName = "export-$fileId.csv";

Loader::includeModule("iblock");

$request = Context::getCurrent()->getRequest();
$sezonValue = $request->getQuery('sezon');

if (empty($sezonValue)) {
    $path = $request->getRequestedPageDirectory() . 'form.php?error=Поле сезона не валидно!';
    LocalRedirect($path);
    die();
}

$iblockId = 9;
$arFilter = [
    "IBLOCK_ID" => $iblockId,
    'ACTIVE' => 'Y',
    "DETAIL_TEXT" => false,
    '!SECTION_ID' => false,
    'PROPERTY_SEZON_RUS' => $sezonValue
];
$arSelect = [
    "ID",
    "NAME",
    "PREVIEW_TEXT",
    "DETAIL_TEXT",
    "PREVIEW_PICTURE",
    "PROPERTY_ARTICLE",
    'PROPERTY_TSVET',
    'PROPERTY_SEZON_RUS'
];

$pageSize = 200; // величина чанка
$page = 1;


$csvFile = fopen(__DIR__  . "/$fileName", "w");
// BOM кодировка
fwrite($csvFile, "\xEF\xBB\xBF");
$headers = ["Картинка для анонса", "Код", "Описание анонсы",  "Название", "Цвет", "Сезон", "Детальное описание"];

fputcsv($csvFile, $headers, ";");
do {
    $rsItems = CIBlockElement::GetList(
        [],
        $arFilter,
        false,
        ["nPageSize" => $pageSize, "iNumPage" => $page],
        $arSelect
    );

    while ($item = $rsItems->Fetch()) {
        $imageLink = CFile::GetPath($item['PREVIEW_PICTURE']);
        if (empty($imageLink)) {
            $imageLink = null;
        } else {
            $imageLink = ($request->isHttps() ? 'https://' : 'http://') .
                $request->getServer()->getServerName() . $imageLink;
        }
        $data = [
            $imageLink,
            $item['PROPERTY_ARTICLE_VALUE'],
            $item['PREVIEW_TEXT'],
            $item['NAME'],
            $item['PROPERTY_TSVET_VALUE'],
            $item['PROPERTY_SEZON_RUS_VALUE'],
            $item["DETAIL_TEXT"],
        ];
        fputcsv($csvFile, $data, ";");
    }

    $page++;
} while ($page <= $rsItems->NavPageCount);

fclose($csvFile);

http_response_code(200);
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="export.csv"');

readfile(__DIR__ . "/$fileName");
unlink(__DIR__ . "/$fileName");
