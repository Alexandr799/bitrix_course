<?php
require_once $_SERVER["DOCUMENT_ROOT"] .  '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Context;
use Bitrix\Main\Web\Http\Response;
use Bitrix\Main\Web\Json;

$request = Context::getCurrent()->getRequest();
if ($request->isPost()) {
    $data = $request->getJsonList()->getValues();

    if (empty($data['first_name']) || empty($data['last_name']) || (!is_bool($data['has_access'])) || empty($data['phone'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo Json::encode(['data' => 'Please fill in all required fields.', 'data1'=> $data]);
        die;
    }

    $result = UsersFromFormTable::add([
        'NAME' => $data['first_name'],
        'LAST_NAME' => $data['last_name'],
        'HAS_ACCESS' => $data['has_access'] ? 1 : 0,
        'PHONE_NUMBER' => $data['phone'],
    ]);

    if ($result->isSuccess()) {
        echo Json::encode(['data' => 'Data added successfully.']);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        var_dump($result->getErrorMessages());
        echo Json::encode(['data' => 'Server error']);
    }
} else {
    http_response_code(404);
    header('Content-Type: application/json');
    echo Json::encode(['data' => "not found"]);
}
