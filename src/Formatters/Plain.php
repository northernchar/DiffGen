<?php

namespace Differ\Formatters;

use function Differ\Utils\isAssoc;
use function Functional\flatten;

function plain(mixed $node)
{
    $iter = function ($node, $path) use (&$iter) {
        $isNode = (
            isAssoc($node) && array_key_exists('data', $node)
            ) || (
                !isAssoc($node) && is_array($node) && array_key_exists('data', $node[0])
            );

        if (!$isNode) {
            return [];
        }

        $changes = array_reduce($node, function ($acc, $item) use (&$iter, $path) {
            $data = $item["data"];
            $statusCode = $item['meta']['status'];
            $key = array_key_first($data);
            $val = $data[$key];
            $currentPath = $path === '' ? "{$key}" : "{$path}.{$key}";

            if ($statusCode === 0) {
                return [...$acc, $iter($val, $currentPath)];
            }
            if ($statusCode === 1) {
                $value = plainToString($val);
                return [...$acc, "Property '{$currentPath}' was added with value: {$value}"];
            }
            if ($statusCode === -1) {
                return [...$acc, "Property '{$currentPath}' was removed"];
            }
            if ($statusCode === 2) {
                $removed = $val[0]['data'];
                $added = $val[1]['data'];
                $key = array_key_first($removed);
                $removedVal = plainToString($removed[$key]);
                $addedVal = plainToString($added[$key]);
                return [...$acc, "Property '{$currentPath}' was updated. From {$removedVal} to {$addedVal}"];
            }
        }, []);
        $flattened = flatten($changes);
        $result = implode("\n", $flattened);

        return $result;
    };

    return $iter($node, '');
}

function plainToString(mixed $value): string
{
    if (!is_array($value)) {
        if ($value === null) {
            return 'null';
        }
        return var_export($value, true);
    }
    return "[complex value]";
}
