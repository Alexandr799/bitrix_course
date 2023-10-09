<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Context;
use Bitrix\Main\Loader;

// // если требуется аутенцификация

// /**
//  * @var CUser $USER
//  */
// global $USER;
// $permissionsGroups = [1, 2, 3, 4];

// $groups = explode(',', $USER->GetGroups());
// $auth = false;

// foreach ($permissionsGroups as $id) {
//     if (in_[$id, $groups)) {
//         $auth = true;
//         break;
//     }
// }

// if (!$auth) {
//     http_response_code(403);
//     header('content-type: application/json');
//     echo json_encode(['answer' => 'permission denied']);
//     return;
// }

$fileRandomId = rand(1, 999999999);
$fileName = "export-$fileRandomId.csv";

Loader::includeModule("iblock");

$request = Context::getCurrent()->getRequest();
$iblockId = 9;
$arFilter = ["IBLOCK_ID" => $iblockId];
$arSelect = [
    "ID",
    "NAME",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    "PROPERTY_ARTICLE",
    'PROPERTY_TSVET',
    'PROPERTY_SEZON_RUS'
];

$pageSize = 200; // величина чанка
$page = 1;


$csvFile = fopen(__DIR__  . "/$fileName", "w");
$headers = ["Картинка для анонса", "Код", "Описание анонсы",  "Название", "Цвет", "Сезон"];

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
