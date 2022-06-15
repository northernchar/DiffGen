<?php

namespace Gendiff\Utils;

function getExFromFile(string $path): string
{
    $exploded = explode('.', $path);
    return $exploded[count($exploded) - 1];
}

function toString(mixed $value): string
{
    return trim(var_export($value, true), "'");
}
