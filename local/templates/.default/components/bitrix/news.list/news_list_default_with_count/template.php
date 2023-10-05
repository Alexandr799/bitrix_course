<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="news-list">
    <? if ($arParams["DISPLAY_TOP_PAGER"]) : ?>
        <?= $arResult["NAV_STRING"] ?><br />
    <? endif; ?>
    <?php
    $count = count($arResult["ITEMS"]);
    $this->SetViewTarget('count_news');
    ?>
    <?php foreach ($arResult['PRODUCT'] as $product) { ?>
        <div>
            Количетсво продуктов цвет: <?php echo $product['NAME'] ?> <?php echo $product['CNT'] ?>
        </div>
    <?php } ?>
    <?php
    $this->EndViewTarget();
    ?>
    <? foreach ($arResult["ITEMS"] as $arItem) : ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <p class="news-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
            <? if ($arParams["DISPLAY_DATE"] != "N" && $arItem["DISPLAY_ACTIVE_FROM"]) : ?>
                <span class="news-date-time"><? echo $arItem["DISPLAY_ACTIVE_FROM"] ?></span>
            <? endif ?>
            <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]) : ?>
                <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])) : ?>
                    <b><? echo $arItem["NAME"] ?></b><br />
                <? else : ?>
                    <b><? echo $arItem["NAME"] ?></b><br />
                <? endif; ?>
            <? endif; ?>
            <? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]) : ?>
                <? echo $arItem["PREVIEW_TEXT"]; ?>
            <? endif; ?>
            <? if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])) : ?>
        <div style="clear:both"></div>
    <? endif ?>
    <? foreach ($arItem["FIELDS"] as $code => $value) : ?>
        <small>
            <?php if ('IBLOCK_FIELD_DETAIL_TEXT' === "IBLOCK_FIELD_" . $code) { ?>
                <?php echo Loc::getMessage('IBLOCK_FIELD_DETAIL_TEXT_CUSTOM'); ?>:&nbsp;<?= $value; ?>
            <?php } else { ?>
                <?= Loc::getMessage("IBLOCK_FIELD_" . $code) ?>:&nbsp;<?= $value; ?>
            <?php } ?>
        </small><br />
    <? endforeach; ?>
    <? foreach ($arItem["DISPLAY_PROPERTIES"] as $pid => $arProperty) : ?>
        <small>
            <?= $arProperty["NAME"] ?>:&nbsp;
            <? if (is_array($arProperty["DISPLAY_VALUE"])) : ?>
                <?= implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]); ?>
            <? else : ?>
                <?= $arProperty["DISPLAY_VALUE"]; ?>
            <? endif ?>
        </small><br />
    <? endforeach; ?>
    </p>
<? endforeach; ?>
<? if ($arParams["DISPLAY_BOTTOM_PAGER"]) : ?>
    <br /><?= $arResult["NAV_STRING"] ?>
<? endif; ?>
</div>
