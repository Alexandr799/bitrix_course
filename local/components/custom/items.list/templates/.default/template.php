<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<ul>
    <?php foreach ($arResult["ITEMS"] as $arItem) : ?>
        <li>ID товара: <?= $arItem["ID"] ?>, Название товара: <?= $arItem["NAME"] ?> Значение проперти: <?php echo $arItem["PROPERTY_TEST_VALUE"] ?></li>
    <?php endforeach; ?>
</ul>
