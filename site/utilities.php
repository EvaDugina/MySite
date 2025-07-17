<?php

function getFileBasename(string $file_path): string
{
    $file_info = pathinfo($file_path);
    $basename = basename($file_path, "." . $file_info['extension']);
    $basename = str_replace("$", "", $basename);
    $splitted_name = explode("__", $basename);
    if (count($splitted_name) > 1)
        return $splitted_name[1];
    $splitted_name = explode("_", $basename);
    if (count($splitted_name) > 1)
        return "";
    return $basename;
}

function getFolderBasename(string $file_path): string
{
    $basename = basename($file_path);
    $basename = str_replace("$", "", $basename);
    $splitted_name = explode("_", $basename);
    if (count($splitted_name) > 1)
        return $splitted_name[1];
    return $basename;
}

function sortFiles(array $files): array
{
    sort($files, SORT_NATURAL);
    return $files;
}
