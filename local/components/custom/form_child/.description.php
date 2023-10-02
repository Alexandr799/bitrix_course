<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = [
    'NAME' => GetMessage('F_NAME'),
    'DESCRIPTION' => GetMessage('F_DESCRIPTION'),
    'CACHE_PATH' => 'Y',
    'SORT' => 10,
    'PATH' => [
        'ID' => 'custom',
        'NAME' =>  GetMessage('F_GROUP_NAME'),
        'SORT' => 10,
    ],
];
