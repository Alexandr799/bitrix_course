<?php

$APPLICATION->IncludeComponent(
    "custom:form.result.new",
    "form_default",
    [
        "CACHE_TIME" => "3600",
        "CACHE_TYPE" => "A",
        "CHAIN_ITEM_LINK" => "",
        "CHAIN_ITEM_TEXT" => "",
        "EDIT_URL" => "",
        "IGNORE_CUSTOM_TEMPLATE" => "N",
        "LIST_URL" => "",
        "SEF_FOLDER" => $arResult["DETAIL_URL"],
        "SEF_MODE" => "Y",
        "SUCCESS_URL" => "/success/",
        "USE_EXTENDED_ERRORS" => "N",
        "WEB_FORM_ID" => "3",
        "CUSTOM_DATA1" => $arResult["VARIABLES"]['ELEMENT_ID'],
    ],
    $component
);
