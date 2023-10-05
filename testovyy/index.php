<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тестовый");
?><?$APPLICATION->IncludeComponent(
	"custom:vacacies",
	"",
	Array(
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"IBLOCK_ID" => "8",
		"IBLOCK_TYPE" => "rest_entity",
		"NEWS_COUNT" => "1",
		"SEF_MODE" => "N",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"VARIABLE_ALIASES" => Array("ELEMENT_ID"=>"ID","FORM_ID"=>"FORM_ID"),
		"WEB_FORM_ID" => ""
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>