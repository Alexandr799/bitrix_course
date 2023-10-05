<?php

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    ".default",
    [
        "CACHE_FILTER" => $arParams["CACHE_FILTER"],
        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "COMPONENT_TEMPLATE" => "news_list_default",
        "DETAIL_URL" => $arResult["DETAIL_URL"],
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "NEWS_COUNT" => $arParams["NEWS_COUNT"],
        "SORT_BY1" => $arParams["SORT_BY1"],
        "SORT_ORDER1" => $arParas["SORT_ORDER1"],
    ],
    $component
);
