<?
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("CIBLockHandler", "OnBeforeIBlockElementUpdateHandler"));

class CIBLockHandler
{
    // создаем обработчик события "OnBeforeIBlockElementUpdate"
    function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {
		if($arFields['IBLOCK_ID'] == CATALOG_IBLOCK_ID){
			$db_props = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], array("sort" => "asc"), Array("CODE"=>"PRICE"));
			if($ar_props = $db_props->Fetch()){

				if(strlen($arFields['PROPERTY_VALUES'][$ar_props["ID"]][$ar_props["PROPERTY_VALUE_ID"]]['VALUE'])>0){
					$arFields['PROPERTY_VALUES'][$ar_props["ID"]][$ar_props["PROPERTY_VALUE_ID"]]['VALUE'] = preg_replace("/[^\d]/","",$arFields['PROPERTY_VALUES'][$ar_props["ID"]][$ar_props["PROPERTY_VALUE_ID"]]['VALUE']);
				}
			}
		}
       
    }
}

AddEventHandler("main", "OnBeforeEventAdd", array("CMainHandler", "OnBeforeEventAddHandler"));
AddEventHandler("main", "OnBeforeUserAdd", Array("CMainHandler", "OnBeforeUserAddHandler"));
class CMainHandler
{
	function OnBeforeUserAddHandler(&$arFields)
    {
        if($arFields["LAST_NAME"] == $arFields["NAME"])
        {
            global $APPLICATION;
            $APPLICATION->throwException("Имя и Фамилия одинаковы!");
            return false;
        }
    }
	
	function OnBeforeEventAddHandler(&$event, &$lid, &$arFields)
	{
		
		if($event == "FEEDBACK_FORM"){
			if(CMOdule::IncludeModule("iblock"))
			{
				$el = new CIBlockElement;
				$arLoadProductArray=array(
					"IBLOCK_ID" => FEEDBACK_IBLOCK_ID,
					"NAME" => $arFields["AUTHOR"],
					"DETAIL_TEXT" => $arFields["TEXT"],
					"DATE_ACTIVE_FROM" => ConvertTimeStamp(false,"FULL"),
				);
				$el->Add($arLoadProductArray);
			}
		}
		

	}
}

?>