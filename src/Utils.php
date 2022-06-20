<?php

namespace Differ\Utils;

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
    if (!is_array($value)) {
        return false;
    }

    if (array() === $value) {
        return false;
    }

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

function getAst($key, $value, int $status, $type = 'nested')
{
    return ["data" => ["{$key}" => $value], "meta" => ["status" => $status, "type" => $type]];
}

function getValueType($value)
{
    if (isAssoc($value)) {
        return 'nested';
    }
    if (!isAssoc($value) && is_array($value)) {
        return 'array';
    }
    return "leaf";
}
