<?php

namespace Gendiff\Formatters;

use function Gendiff\Utils\toString;

function getGitFormat(array $changes, string $replacer = " ", $spacesCount = 2): string
{
    $mapped = array_map(function ($element) use ($replacer, $spacesCount) {
        $item = $element[0];
        $key = array_key_first($item);
        $value = toString($item[$key]);
        $indents = str_repeat($replacer, $spacesCount);

        switch ($element['status']) {
            case "deleted":
                $status = "-";
                break;
            case "added":
                $status = "+";
                break;
            default:
                $status = " ";
                break;
        }

        return "{$indents}{$status} {$key}: {$value}";
    },
    $changes);
    $formatted = implode("\n", $mapped);
    $result = "{\n{$formatted}\n}";

    return $result;
}

function getPlainFormat(array $changes)
{

}

function getJSONformat(array $changes)
{
    
}