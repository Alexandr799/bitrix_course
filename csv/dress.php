<?php
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Context;
use Bitrix\Main\Loader;

/**
 * @var CUser $USER
 */
global $USER;

Loader::includeModule("iblock");

$iblockId = 9;
$arFilter = [
    "IBLOCK_ID" => $iblockId,
    'ACTIVE' => 'Y',
    "DETAIL_TEXT" => false,
    '!SECTION_ID' => false,
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

$request = Context::getCurrent()->getRequest();
$path = $request->getRequestedPage();

$sezonValue = $request->getQuery('sezon');

if (!empty($sezonValue) && is_string($sezonValue)) {
    $arFilter['PROPERTY_SEZON_RUS'] = $sezonValue;
    $fileId = $USER->GetID();
    $fileName = "export-$fileId.csv";

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
} else {
    $arFilter['!PROPERTY_SEZON_RUS'] = false;
    $counter = CIBlockElement::GetList([], $arFilter, ['PROPERTY_SEZON_RUS']);

    $propsList = [];
    while ($data = $counter->Fetch()) {
        $propsList[] = $data["PROPERTY_SEZON_RUS_VALUE"];
    }
    $error = $request->getQuery('error');
    $path = $path . '?download=true';
?>

    <!DOCTYPE html>
    <html lang="ru">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Форма для выгрузки данных</title>
    </head>

    <body style="font-family:sans-serif">
        <h1 style="text-align:center;padding-top:40px;">Форма для выгрузки данных</h1>
        <div style="display:flex;justify-content:center;align-items:center">
            <form action="<?php echo $path ?>">
                <?php if (!empty($error)) { ?>
                    <div style="padding:10x 0;color:red"><?php echo $error  ?></div>
                <?php } ?>
                <div style="margin-bottom:20px">Выберите сезон для выгрузки</div>
                <div style="width:100%;margin-bottom:20px">
                    <select style="width:100%" name="sezon" id="sezon">
                        <?php foreach ($propsList as $prop) { ?>
                            <option value="<?php echo $prop ?>"><?php echo $prop ?></option>
                        <?php } ?>
                    </select>
                </div>
                <button style="width:100%">Скачать</button>
            </form>
        </div>
    </body>

    </html>
<?php
}
