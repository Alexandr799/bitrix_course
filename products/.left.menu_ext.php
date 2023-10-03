<?php
global $APPLICATION;
$aMenuLinks = $APPLICATION->IncludeComponent(
	"bitrix:menu.sections",
	"",
	array(
		"IS_SEF" => "N",
		"ID" => $_REQUEST["ID"],
		"IBLOCK_TYPE" => "rest_entity",
		"IBLOCK_ID" => "6",
		"SECTION_URL" => "#SITE_DIR#",
		"DEPTH_LEVEL" => "2",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "36000000",
		"SEF_BASE_URL" => "/catalog/phone/",
		"SECTION_PAGE_URL" => "#SECTION_ID#/",
		"DETAIL_PAGE_URL" => "#SECTION_ID#/#ELEMENT_ID#"
	),
	false
);

