<div id="panel"><? $APPLICATION->ShowPanel(); ?></div>

<div id="header"><img src="<?= SITE_TEMPLATE_PATH ?>/images/logo.jpg" id="header_logo" height="105" alt="" width="508" border="0" />
    <div id="header_text">
        <? $APPLICATION->IncludeFile(
            $APPLICATION->GetTemplatePath("include_areas/company_name.php"),
            array(),
            array("MODE" => "html")
        ); ?>
    </div>
    <a href="/" title="Главная" id="company_logo"></a>
    <div id="header_menu">
        <? $APPLICATION->IncludeFile(
            $APPLICATION->GetTemplatePath("include_areas/header_icons.php"),
            array(),
            array("MODE" => "php")
        ); ?>
    </div>
</div>
<? $APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"horizontal_multilevel_default_top", 
	array(
		"ROOT_MENU_TYPE" => "top",
		"MAX_LEVEL" => "3",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"COMPONENT_TEMPLATE" => "horizontal_multilevel_default_top",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N"
	),
	false
); ?>
<div id="zebra"></div>

<table id="content">
    <tbody>
        <tr>
            <td>
                <? $APPLICATION->IncludeComponent(
                    "bitrix:main.include",
                    "",
                    array(
                        "AREA_FILE_RECURSIVE" => "Y",
                        "AREA_FILE_SHOW" => "sect",
                        "AREA_FILE_SUFFIX" => "inc",
                        "EDIT_TEMPLATE" => ""
                    )
                ); ?>

                <div style="padding:20px 20px;">
                    <a href="tel:<?php $APPLICATION->IncludeFile(
                                        $APPLICATION->GetTemplatePath("include_areas/tel.php"),
                                        array(),
                                        array("MODE" => "php")
                                    ) ?>">
                        <?php $APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/tel.php"),
                            array(),
                            array("MODE" => "text")
                        ) ?>
                    </a>
                </div>
                <?php $APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"vertical_multilevel_default", 
	array(
		"ROOT_MENU_TYPE" => "left",
		"MAX_LEVEL" => "2",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"COMPONENT_TEMPLATE" => "vertical_multilevel_default",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N"
	),
	false
);
                $APPLICATION->IncludeComponent(
                    "bitrix:system.auth.form",
                    "auth_default_custom_template",
                    array(
                        "FORGOT_PASSWORD_URL" => "/user/",
                        "PROFILE_URL" => "/user/profile.php",
                        "REGISTER_URL" => "/user/register.php",
                        "SHOW_ERRORS" => "Y",
                        "COMPONENT_TEMPLATE" => "auth_default_custom_template"
                    ),
                    false
                );
                ?>
            </td>
            <td class="main-column">
                <div id="navigation">
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:breadcrumb",
                        ".default",
                        array(
                            "START_FROM" => "0",
                            "PATH" => "",
                            "SITE_ID" => ""
                        )
                    ); ?> </div>
                <h1 id="pagetitle"><? $APPLICATION->ShowTitle(false) ?></h1>
                <?php
                /**
                 * @var CMain $APPLICATION;
                 */
                $APPLICATION->ShowViewContent('date_news');
                $APPLICATION->ShowViewContent('count_news');
                ?>
