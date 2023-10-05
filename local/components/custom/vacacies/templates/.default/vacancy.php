<?php
/**
 * @var CMain $APPLICATION
 */

$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"vacancy_default",
	array(
		"DETAIL_URL" => $arResult["DETAIL_URL"],
		"ELEMENT_ID" =>  $arResult["VARIABLES"]['ELEMENT_ID'],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"IBLOCK_TYPE" => "rest_entity",
		"IBLOCK_URL" => "",
        "FIELD_CODE" => array(
            0 => "NAME",
            1 => "PREVIEW_TEXT",
            2 => "DETAIL_PICTURE",
            3 => "DATE_ACTIVE_TO",
            4 =>  "DATE_ACTIVE_FROM",
            5 => "DETAIL_TEXT",
        ),
	),
	$component
);
