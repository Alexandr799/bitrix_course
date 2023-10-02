<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = [
    'NAME' => GetMessage('AL_NAME'),
    'DESCRIPTION' => GetMessage('AL_DESCRIPTION'),
    'CACHE_PATH' => 'Y',
    'SORT' => 10,
    'PATH' => [
        'ID' => 'custom',
        'NAME' =>  GetMessage('AL_GROUP_NAME'),
        'SORT' => 10,
    ],
];
