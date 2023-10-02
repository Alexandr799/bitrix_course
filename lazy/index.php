<?php

class App
{
    const PLACEHOLDER = "TITLE_HERE";
    private $title = '';
    function setTitle($t)
    {
        $this->title = $t;
    }

    function getTitle()
    {
        return $this->title;
    }

    function showTitle()
    {
        echo self::PLACEHOLDER;
    }
}

$app = new App();

require __DIR__ . '/header.php';
$app->showTitle();
echo 'hello bitrix!';

$app->setTitle('Тайтл');

require __DIR__ . '/footer.php';
