<?php

/**
 * @var CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Демонстрационная версия продукта «1С-Битрикс: Управление сайтом»");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle("Главная страница");
$APPLICATION->IncludeFile(SITE_DIR . '/include/head-text.php', [], ["MODE" => 'html']);
?><div>
	 это самая главная страница <br>
</div>
<div>
 <br>
</div>
<div>
 <img width="667" alt="Снимок экрана от 2023-10-05 11-43-44.png" src="/upload/medialibrary/239/zojzmegrdglcz9dmqybhigx3rauf3x5x/Снимок%20экрана%20от%202023-10-05%2011-43-44.png" height="430" title="Снимок экрана от 2023-10-05 11-43-44.png"><br>
</div><?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>