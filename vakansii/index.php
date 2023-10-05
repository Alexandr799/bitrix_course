<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("вакансии");
?><?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new", 
	"form_default", 
	array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"EDIT_URL" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "Y",
		"LIST_URL" => "",
		"SEF_FOLDER" => "/vakansii/",
		"SEF_MODE" => "Y",
		"SUCCESS_URL" => "/success/",
		"USE_EXTENDED_ERRORS" => "N",
		"WEB_FORM_ID" => "3",
		"COMPONENT_TEMPLATE" => "form_default"
	),
	false
);?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>