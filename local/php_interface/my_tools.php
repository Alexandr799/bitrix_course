<?php
function test_dump(...$data)
{
    /**
     * @var CUser $USER
     */
    global $USER;
    if ($USER->IsAdmin()) {
        var_dump(...$data);
    }
}
