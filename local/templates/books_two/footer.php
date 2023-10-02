<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);

?> </td>
<td class="right-column">
    <? $APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "sect",
            "AREA_FILE_SUFFIX" => "inc",
            "AREA_FILE_RECURSIVE" => "N",
            "EDIT_MODE" => "html",
            "EDIT_TEMPLATE" => "sect_inc.php"
        )
    ); ?>
    <? $APPLICATION->IncludeComponent(
        "bitrix:main.include",
        "",
        array(
            "AREA_FILE_SHOW" => "page",
            "AREA_FILE_SUFFIX" => "inc",
            "AREA_FILE_RECURSIVE" => "N",
            "EDIT_MODE" => "html",
            "EDIT_TEMPLATE" => "page_inc.php"
        )
    ); ?> </td>
</tr>
</tbody>
</table>

<!--BANNER_BOTTOM-->

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
        "horizontal_multilevel",
        array(
            "ROOT_MENU_TYPE" => "bottom",
            "MAX_LEVEL" => "3",
            "CHILD_MENU_TYPE" => "",
            "USE_EXT" => "Y",
            "MENU_CACHE_TYPE" => "A",
            "MENU_CACHE_TIME" => "3600",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS" => array()
        )
    ); ?>
</div>
</body>

</html>
