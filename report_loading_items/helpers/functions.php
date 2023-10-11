<?php

/**
 * Выводит ошибку и заканчивает выполнение процесса
 * @param string $text
 * @param int $code
 * @param string $errorLogTe
 * @return void
 */
function setError(string $text, int $code, string $errorLogText = '')
{
    http_response_code($code);
    header("Content-Type: application/json");
    echo json_encode([
        'errorText' => $text
    ]);
    if ($errorLogText != '') {
        error_log($errorLogText);
    }
}

/**
 * Выводит json успещного ответа и заканчивает выполнение процесса
 * @param string|array $data
 * @param int code
 * @return void
 */
function setSuccess(mixed $data, int $code = 200)
{
    http_response_code($code);
    header("Content-Type: application/json");
    echo json_encode(['answer' => $data]);
}

/**
 * @param string $text
 * @param string $pathTologFile
 * @return void
 */
function writeLog(string $text, string $pathTologFile)
{
    file_put_contents($pathTologFile, $text . "\r\n", FILE_APPEND);
}

/**
 * Передается массив нзваний столбцов, на выходе функция выдает строку с агрегационной функция sql
 * которая создает столбец с процентом пропусков
 *  @param array $fields
 *  @param string $colName
 *  @return string
 */
function makeColumnWithNullCount(array $fields, $colName = 'NULL_COUNT'): string
{
    $agg = 'ROUND(((';
    $count = count($fields);
    if ($count === 0) return '';
    foreach ($fields as $i => $field) {
        if ($count - 1 === $i) {
            $agg .= "($field IS NULL)) / $count) * 100)  AS  $colName";
        } else {
            $agg .= "($field IS NULL) + ";
        }
    }
    return $agg;
}


/**
 * Отправляет CSV-файл клиенту для скачивания.
 *
 * @param string $path Путь к CSV-файлу для загрузки.
 * @param int $code HTTP-код ответа (по умолчанию 200).
 * @param string $clientFileName Имя файла для клиента (по умолчанию 'export.csv').
 * @param bool $deleteAfterLoad Флаг удаления файла после загрузки (по умолчанию true).
 */
function responseCsvDownLoad(string $path, int $code = 200, string $clientFileName = 'export.csv',  bool $deleteAfterLoad = true)
{
    http_response_code($code);
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="' . $clientFileName . '"');

    readfile($path);

    if ($deleteAfterLoad) unlink($path);
}

/**
 * Формирует where строку для вставки в запрос
 *
 * @param array $params Путь к CSV-файлу для загрузки.
 * @param null|callable $filterFun HTTP-код ответа (по умолчанию 200).
 * @return string
 */
function makeWhereRow(array $params, mixed $filterFunc = null): string
{
    if (is_null($filterFunc)) {
        $filterFunc = function ($value) {
            return $value;
        };
    }

    $where = 'WHERE';
    if (isset($params['filter']['kod']) && $params['filter']['kod'] != '') {
        $kod = $filterFunc($params['filter']['kod']);
        $where .= " KOD = '$kod' ";
    }

    if (isset($params['filter']['sezon']) && $params['filter']['sezon'] != '') {
        $sezon = $filterFunc($params['filter']['sezon']);
        $where .= $where === 'WHERE' ? " SEZON = '$sezon' " : "AND SEZON = '$sezon' ";
    }

    if (isset($params['filter']['action']) && $params['filter']['action'] != '') {
        $action = $filterFunc($params['filter']['action']);
        $where .= $where === 'WHERE' ? " $action IS NULL " : "AND $action iS NULL ";
    }

    if (isset($params['filter']['done_action']) && $params['filter']['done_action'] != '') {
        $action = $filterFunc($params['filter']['done_action']);
        $where .= $where === 'WHERE' ? " $action IS NOT NULL " : "AND $action iS NOT NULL ";
    }

    return  $where === 'WHERE' ? '' : $where;
}


/**
 * Строит URL с добавленными или измененными параметрами запроса.
 *
 * @param array $query Массив с текущими параметрами запроса.
 * @param string $referer Хост (например, "http://example.com").
 * @param string $key Ключ параметра запроса для добавления или изменения.
 * @param string $value Значение параметра запроса.
 *
 * @return string Возвращает URL с обновленными параметрами запроса.
 */
function makeLinkWithQuery(array $query, string $referer,  string $key, string $value): string
{
    $referer = explode('?', $referer)[0];
    $query[$key] = $value;
    $getparams = http_build_query($query);

    return $referer . '?' . $getparams;
}

/**
 *
 * @param string $date Дата для проверки.
 * @param string $format Формат даты (по умолчанию 'Y-m-d').
 * @return bool Возвращает true, если дата действительна в указанном формате, иначе false.
 */
function validateDate($date, $format = 'Y-m-d'): bool
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
