<?php

define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


use Bitrix\Main\Loader;
use Bitrix\Main\Context;

Loader::includeModule("iblock");

$request = Context::getCurrent()->getRequest();
$error = Context::getCurrent()->getRequest()->getQuery('error');

$formActionPath = $request->getRequestedPageDirectory() . 'dress.php';
$idBLock = 9;

$counter = CIBlockElement::GetList(["SORT" => "ASC"], [
    "IBLOCK_ID" => $iblockId,
    'ACTIVE' => 'Y',
    "DETAIL_TEXT" => false,
    '!SECTION_ID' => false,
    '!PROPERTY_SEZON_RUS' => false,
], ['PROPERTY_SEZON_RUS']);

$propsList = [];
while ($data = $counter->Fetch()) {
    $propsList[] = $data["PROPERTY_SEZON_RUS_VALUE"];
}
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
        <form action="<?php echo $formActionPath ?>">
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
            <button style="width:100%">Отправить</button>
        </form>
    </div>
</body>

</html>
