<?php

namespace Differ\Utils;

function getExFromFile(string $path): string
{
    $exploded = explode('.', $path);
    return $exploded[count($exploded) - 1];
}

function toString(mixed $value): string
{
    if ($value === null) {
        return 'null';
    }
    return trim(var_export($value, true), "'");
}

function isAssoc(mixed $value)
{
    if (!is_array($value)) {
        return false;
    }

    if (array() === $value) {
        return false;
    }

    return array_keys($value) !== range(0, count($value) - 1);
}

function isJson(string $file)
{
    $extension = getExFromFile($file);
    return $extension === "json";
}

function isYaml(string $file)
{
    $extension = getExFromFile($file);
    return $extension === "yaml" || $extension === "yml";
}

function getAst(string $key, mixed $value, int $status, string $type = 'nested')
{
    return ["data" => ["{$key}" => $value], "meta" => ["status" => $status, "type" => $type]];
}

function getValueType(mixed $value)
{
    if (isAssoc($value)) {
        return 'nested';
    }
    if (!isAssoc($value) && is_array($value)) {
        return 'array';
    }
    return "leaf";
}
