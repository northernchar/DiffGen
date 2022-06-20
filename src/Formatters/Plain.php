<?php

namespace Gendiff\Formatters;

use function Gendiff\Utils\isAssoc;
use function Functional\flatten;

function plain($node)
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
                $acc[] = $iter($val, $currentPath);
                return $acc;
            }
            if ($statusCode === 1) {
                $val = !is_array($val) ? var_export($val, true) : "[complex value]";
                $acc[] = "Property '{$currentPath}' was added with value: {$val}";
                return $acc;
            }
            if ($statusCode === -1) {
                $acc[] = "Property '{$currentPath}' was removed";
                return $acc;
            }
            if ($statusCode === 2) {
                $removed = $val[0]['data'];
                $added = $val[1]['data'];
                $removedKey = array_key_first($removed);
                $addedKey = array_key_first($added);
                $removedVal = !is_array($removed[$removedKey]) ?
                    var_export($removed[$removedKey], true) :
                        "[complex value]";

                $addedVal = !is_array($added[$addedKey]) ?
                    var_export($added[$addedKey], true) :
                        "[complex value]";

                $acc[] = "Property '{$currentPath}' was updated. From {$removedVal} to {$addedVal}";
                return $acc;
            }
        }, []);
        $flattened = flatten($changes);
        $result = implode("\n", $flattened);

        return $result;
    };

    return $iter($node, '');
}
