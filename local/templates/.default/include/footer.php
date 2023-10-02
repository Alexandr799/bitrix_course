<div id="footer">
    <div style="text-align:center;">
        <a href="tel:<?php $APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/tel.php"),
                            array(),
                            array("MODE" => "text")
                        ) ?>">
            <?php $APPLICATION->IncludeFile(
                $APPLICATION->GetTemplatePath("include_areas/tel.php"),
                array(),
                array("MODE" => "text")
            ) ?>
        </a>
    </div>
    <? $APPLICATION->IncludeFile(
        $APPLICATION->GetTemplatePath("include_areas/copyright.php"),
        array(),
        array("MODE" => "html")
    ); ?>
    <? $APPLICATION->IncludeComponent(
	"bitrix:menu",
	"horizontal_multilevel_default",
	array(
		"ROOT_MENU_TYPE" => "bottom",
		"MAX_LEVEL" => "3",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "Y",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"COMPONENT_TEMPLATE" => "horizontal_multilevel_default",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N"
	),
	false
); ?>
</div>
