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
		"SEF_FOLDER" => "/testovyy/",
		"SEF_MODE" => "Y",
		"SEF_URL_TEMPLATES" => Array("rezume"=>"#ELEMENT_ID#/form/","vacancies"=>"","vacancy"=>"#ELEMENT_ID#/"),
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"WEB_FORM_ID" => ""
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
