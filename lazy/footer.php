<?php 

$content = ob_get_contents();

ob_clean();

$content = str_replace(
    App::PLACEHOLDER,
    $app->getTitle(),
    $content
);

echo $content;