<?php


class TestFormComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->arResult['AJAX_HANDLER'] = $this->getPath();
        $this->includeComponentTemplate();
    }
}
