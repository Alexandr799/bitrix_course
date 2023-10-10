<?php

/**
 * Выводит ошибку и заканчивает выполнение процесса
 * @param string text
 * @param int code
 * @param string errorLogTe
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
 * @param string|array data
 * @param int code
 * @return void
 */
function setSuccess(mixed $data, int $code = 200)
{
    http_response_code($code);
    header("Content-Type: application/json");
    echo json_encode(['answer' => $data]);
}

function writeLog(string $text, string $pathTologFile)
{
    file_put_contents($pathTologFile, $text . "\r\n", FILE_APPEND);
}

/**
 * @param string|null value
 * @param string alias
 * @param string and
 * @return string
 */
function makeWhereFromParam(mixed $value, string $alias, $and = true, callable $callbackFilter = function (string $value) {
    return $value;
})
{
    $value = $callbackFilter($value);
    $where = isset($value) && $value != '' ? "$alias = $value " : '';
    if ($and &&  $value != '') {
        $where .= 'AND ';
    }
    return $where;
}
