<?php
function test_dump(...$data)
{
    /**
     * @var CUser $USER
     */
    global $USER;
    if ($USER->IsAdmin()) {
        var_dump(...$data);
    } else {
        echo 'здесь должен быть дамп для админа';
    };
}
