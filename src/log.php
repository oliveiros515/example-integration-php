<?php

function writeLog($content)
{
    $txt = date('Y-m-d H:m:s') . ' - ' . $content . PHP_EOL;

    file_put_contents(__DIR__ . '/../bp-' . date('Y-m-d') . '.log', $txt, FILE_APPEND | LOCK_EX);
}