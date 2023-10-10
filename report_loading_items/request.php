<?php

require_once(__DIR__ . '/helpers/functions.php');
require_once(__DIR__ . '/helpers/Report.php');
require_once(__DIR__ . '/db/db.php');


$requestBodyJson = json_decode(file_get_contents('php://input'), true);

if (empty($requestBodyJson["access"])) return setError("Нет токена доступа", 403);

$TABLE_DATA = include_once(__DIR__ . '/db/status_items_table.php');
$TABLE_NAME = $TABLE_DATA['table_name'];
$FIELDS = $TABLE_DATA['fields_fillable'];
$TOKEN = "1b234120bvv602";

$requestAccess = htmlspecialchars($requestBodyJson["access"]);

if ($requestAccess != $TOKEN) return setError('Нет доступа', 403);

if (isset($requestBodyJson["xml_id"]) && isset($requestBodyJson["action"])) {

    $requestXmlId = $requestBodyJson["xml_id"];
    $requestAction = htmlspecialchars(trim($requestBodyJson["action"]));
    $db = new DB();
    if ($requestAction == 'ACTION_1C_CREATE') {

        $exist = $db->exists($TABLE_NAME, '*',  ['XML_ID' => $requestXmlId]);
        if ($exist) return setError('Уже существует данный xml_id', 400);

        $dataCreate = [
            'XML_ID' => $requestXmlId,
            'ACTION_1C_CREATE' => date('Y-m-d')
        ];

        if (htmlspecialchars($requestBodyJson["color"])) {
            $dataCreate['COLOR'] = htmlspecialchars($requestBodyJson["color"]);
        }

        if (htmlspecialchars($requestBodyJson["kod"])) {
            $dataCreate['KOD'] = htmlspecialchars($requestBodyJson["kod"]);
        }

        if (htmlspecialchars($requestBodyJson["SEZON"])) {
            $szn = htmlspecialchars($requestBodyJson["SEZON"]);
            $szn = preg_replace('/[^a-zA-Zа-яА-Я0-9]/ui', '', $szn);
            $szn = str_replace(['a2', 'ь2', 'о2'], ['a 2', 'ь 2', 'о 2'], $szn);
            $dataCreate['SEZON'] = $szn;
        }

        $log = var_export(['date' => date('d.m.Y H:i:s'), 'request' => $dataCreate], true);
        writeLog($log, __DIR__ . '/log.txt');

        $addTable =  $db->insert($TABLE_NAME,  $dataCreate);
        if ($addTable !== false) return setSuccess(['add_data' => $addTable]);

        error_log('Ошибка при записи в базу данных!');
        return setError('Не удалось добавить в базу данных', 500);
    } else if (in_array($requestAction, $FIELDS)) {

        $set = [$requestAction => date('Y-m-d')];
        $where = ['XML_ID' => $requestXmlId];

        $log = var_export(['date' => date('d.m.Y H:i:s'), 'request' => $dataCreate], true);
        writeLog($log, __DIR__ . '/log.txt');

        $update = $db->update($TABLE_NAME,  $set, $where);
        if ($update !== false) return setSuccess(['update_data' => $update]);

        error_log('Ошибка при записи в базу данных!');
        return setError('Не удалось обновить в базе данных', 500);
    } else {
        return setError('Не известно действие', 400);
    }
}

if (isset($requestAccess["xml_site"])) {
    $objReports = new Reports;
    $xml_site = $requestAccess["xml_site"];
    $arResult = $objReports->requestSiteData($xml_site);
    return;
}

if (isset($requestAccess["folder"])) {
    $arResult['status'] = true;
    $arFolder = json_decode($requestAccess["folder"], true);

    //file_put_contents(__DIR__ . '/log.txt', var_export($arFolder, true));
    //file_put_contents(__DIR__ . '/log.txt', var_export(['date'=> date('d.m.Y H:i:s'), 'obmen' => $arFolder], true)."\r\n", FILE_APPEND);

    $objReports = new Reports;

    $arKodColor = [];
    foreach ($arFolder as $folderPath) {
        $arPath = explode('\\', $folderPath);
        $lastFolder = array_pop($arPath);

        //выбирем только папки с кодом и цветом
        if (substr_count($lastFolder, '_')) {
            $arItemPath = explode('_', $lastFolder);
            $arKodColor[] = $arItemPath;
        }
    }


    $arResult = $objReports->requestPhoto($arKodColor, 'ACTION_PHOTO_CREATE');
    return;
}

if (isset($requestAccess["folder_processed"])) {
    //$arResult['status'] = true;
    $arFolder = json_decode($requestAccess["folder_processed"], true);

    //file_put_contents(__DIR__ . '/log.txt', var_export($arFolder, true));
    //file_put_contents(__DIR__ . '/log.txt', var_export(['date'=> date('d.m.Y H:i:s'), 'obmen' => $arFolder], true)."\r\n", FILE_APPEND);

    $objReports = new Reports;

    $arKodColor = [];
    foreach ($arFolder as $folderPath) {
        $arPath = explode('\\', $folderPath);
        $lastFolder = array_pop($arPath);

        //выбирем только папки с кодом и цветом
        if (substr_count($lastFolder, '_')) {
            $arItemPath = explode('_', $lastFolder);
            $arKodColor[] = $arItemPath;
        }
    }


    $arResult = $objReports->requestPhoto($arKodColor, 'ACTION_PHOTO_PROCESSED');
    return;
}

return setError('Не верные параметры', 400);
