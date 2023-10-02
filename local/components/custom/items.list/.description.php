<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = [
    'NAME' => GetMessage('IL_NAME'),
    'DESCRIPTION' => GetMessage('IL_DESCRIPTION'),
    'CACHE_PATH' => 'Y',
    'SORT' => 10,
    'PATH' => [
        'ID' => 'custom',
        'NAME' =>  GetMessage('IL_GROUP_NAME'),
        'SORT' => 10,
    ],
];
