<?php

use Bitrix\Main\Loader;

Loader::includeModule('iBlock');


$data = CIBlockElement::GetByID($arParams['CUSTOM_DATA1'])->Fetch();

if (!$data) LocalRedirect('/404', false, 404);
$arResult['VACANT'] = $data['ID'] . ' ' . $data['NAME'];
