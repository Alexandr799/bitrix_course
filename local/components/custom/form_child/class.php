<?php

CBitrixComponent::includeComponentClass('custom:form');
class TestFormComponentChild extends TestFormComponent
{
    public function executeComponent()
    {
        var_dump(TestFormComponentChild::class, 'привет!');
        $this->arResult['AJAX_HANDLER'] = $this->getPath();
        $this->includeComponentTemplate();
    }
}
