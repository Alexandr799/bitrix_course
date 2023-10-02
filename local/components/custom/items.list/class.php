<?php

use Bitrix\Main\Loader;

class ItemsListComponent extends CBitrixComponent
{
    public function getItems()
    {
        Loader::includeModule('iBlock');
        $items = [];
        if ((!empty($this->arParams["SECTION_ID"])) && (!empty($this->arParams["IBLOCK_ID"]))) {
            $arFilter = [
                "IBLOCK_ID" => $this->arParams["IBLOCK_ID"],
                "SECTION_ID" => $this->arParams["SECTION_ID"],
            ];

            $arSelect = ["ID", "NAME", 'PROPERTY_TEST'];

            $rsItems = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

            while ($item = $rsItems->GetNext()) {
                $items[] = $item;
            }
            echo '<br><br><br><br>';
            var_dump($items);
            return $items;
        }
        return [];
    }
    public function executeComponent()
    {
        $this->arResult['ITEMS'] = $this->getItems();

        $this->includeComponentTemplate();
    }

    public function getParameters()
    {
        return [
            "SECTION_ID" => [
                "PARENT" => "BASE",
                "NAME" => "ID раздела",
                "TYPE" => "STRING",
                "DEFAULT" => "",
            ],
            "IBLOCK_ID" => [
                "PARENT" => "BASE",
                "NAME" => "ID инфоблока",
                "TYPE" => "STRING",
                "DEFAULT" => "",
            ],
        ];
    }
}
