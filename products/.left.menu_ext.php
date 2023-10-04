<?php
global $APPLICATION;
$aMenuLinks = $APPLICATION->IncludeComponent(
    "bitrix:menu.sections",
    "",
    array(
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "DEPTH_LEVEL" => "2",
        "DETAIL_PAGE_URL" => "#SECTION_ID#/#ELEMENT_ID#",
        "IBLOCK_ID" => "6",
        "IBLOCK_TYPE" => "rest_entity",
        "IS_SEF" => "Y",
        "SECTION_PAGE_URL" => "#SECTION_ID#/",
        "SEF_BASE_URL" => "/products/"
    )
);


