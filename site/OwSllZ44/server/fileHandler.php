<?php

function readFileSafe(string $fileName): string
{
    $fp = fopen($fileName, 'r+');
    flock($fp, LOCK_SH); // Разделяемая блокировка для чтения
    $content = fread($fp, filesize($fileName) ?: 1);
    fclose($fp);
    return $content;
}

function writeFileSafe(string $fileName, string $data)
{
    $fp = fopen($fileName, 'w');
    flock($fp, LOCK_EX);
    fwrite($fp, $data);
    flock($fp, LOCK_UN);
    fclose($fp);
}
