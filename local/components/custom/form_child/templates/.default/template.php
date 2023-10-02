<?php

/**
 * @var array  $arResult
 * @var CBitrixComponentTemplate $this
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->addExternalJs($this->GetFolder() . '/validator.js')
?>

<form id="custom_form" method="POST" action="<?php echo $arResult['AJAX_HANDLER'] ?>/ajax_handler.php">
    <span>Имя</span>
    <div class="wrapper-input">
        <input type="text" name="first_name">
    </div>
    <span>Фамилия</span>
    <div class="wrapper-input">
        <input type="text" name="last_name">
    </div>
    <span>Имеет доступ</span>
    <div class="wrapper-input">
        <input type="checkbox" name="has_access">
    </div>
    <span>Номер телефона</span>
    <div class="wrapper-input">
        + <input type="text" name="phone">
    </div>
    <input type="submit" value="Отправить">
</form>
