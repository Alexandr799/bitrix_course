<?php

/**
 * @var CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Демонстрационная версия продукта «1С-Битрикс: Управление сайтом»");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
$APPLICATION->IncludeFile(SITE_DIR . '/include/head-text.php', [], ["MODE" => 'html']);
?>
<div>
	 это самая главная страница <br>
</div>
<div>
 <br>
</div>
<div>
 <br>
</div><?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>