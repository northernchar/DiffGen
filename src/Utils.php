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

function isAssoc($value)
{
    if (!is_array($value)) return false;
    if (array() === $value) return false;
    return array_keys($value) !== range(0, count($value) - 1);
}

function isJson($file)
{
    $extension = getExFromFile($file);
    return $extension === "json";
}

function isYaml($file)
{
    $extension = getExFromFile($file);
    return $extension === "yaml" || $extension === "yml";
}


function getStylishStatus($status)
{
    $result = '';

    switch ($status) {
        case 0:
            $result = " ";
            break;
        case 1:
            $result = "+";
            break;
        case -1:
            $result = "-";
            break;
        case 2:
            $result = "+";
            break;
        case -2:
            $result = "-";
            break;
    }
    return $result;
}

function getPlainStatus($status)
{
    $result = '';
    switch ($status) {
        case 0:
            $result = " ";
            break;
        case 1:
            $result = "+";
            break;
        case -1:
            $result = "-";
            break;
    }
    return $result;
}

function getAst($key, $value, int $status, $type = 'assoc')
{
    return ["data" => ["{$key}" => $value], "meta" => ["status" => $status, "type" => $type]];
}

function getValueType($value)
{
    if (isAssoc($value)) {
        return 'assoc';
    }
    if (!isAssoc($value) && is_array($value)) {
        return 'array';
    }
    return is_array($value) ? 'array' :'child';
    
}