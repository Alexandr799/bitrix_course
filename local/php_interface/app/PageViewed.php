<?php

namespace App;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;
use CIBlockElement;

class PageViewed
{
    public function callback()
    {
        $urlString  = Application::getInstance()->getContext()->getRequest()->getRequestUri();
        $url = new Uri($urlString);

        if (Loader::includeModule('iBlock')) {
            $newBlock = new CIBlockElement();
            $newBlock->Add([
                'NAME' => $url,
                'IBLOCK_ID' => 3,
            ]);
        }
    }
}
