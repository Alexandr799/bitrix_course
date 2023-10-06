<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("вакансии");
?><div>
	 <?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new",
	"form_default",
	Array(
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"COMPONENT_TEMPLATE" => "form_default",
		"EDIT_URL" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "Y",
		"LIST_URL" => "",
		"SEF_FOLDER" => "/vakansii/",
		"SEF_MODE" => "Y",
		"SUCCESS_URL" => "/success/",
		"USE_EXTENDED_ERRORS" => "N",
		"WEB_FORM_ID" => "3"
	)
);?>
</div>
<p class="redпп">
	 asdasd<br>
</p><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>