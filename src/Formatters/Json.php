<?php

namespace Differ\Formatters;

use function Differ\Utils\isAssoc;
use function Differ\Utils\toString;

function json($node)
{
    $iter = function ($node) use (&$iter) {
        $isNode = (
            isAssoc($node) && array_key_exists('data', $node)
            ) || (
                !isAssoc($node) && is_array($node) && array_key_exists('data', $node[0])
            );

        if (!$isNode) {
            return $node;
        }

        $newAst = array_reduce($node, function ($acc, $item) use (&$iter) {
            $data = $item['data'];
            $statusCode = $item['meta']['status'];
            $status = getJSONStatus($statusCode);
            $key = array_key_first($data);
            $value = $data[$key];
            if ($statusCode === 2) {
                $removed = $value[0]['data'];
                $added = $value[1]['data'];
                $removedVal = !is_array($removed[$key]) ? toString($removed[$key]) : $removed[$key];
                $addedVal = !is_array($added[$key]) ? toString($added[$key]) : $added[$key];
                $acc[] = [
                    'key' => $key,
                    'old value' => $iter($removedVal),
                    'new value' => $iter($addedVal),
                    'status' => "updated"
                ];
                return $acc;
            }
            $acc[] = $status !== '' ? [
                'key' => $key,
                'value' => $iter($value),
                'status' => $status
                ] : [
                    'key' => $key,
                    'value' => $iter($value)
                ];
            return $acc;
        }, []);

        return $newAst;
    };

    $ast = $iter($node);
    return json_encode($ast, JSON_PRETTY_PRINT);
}

function getJSONStatus($status)
{
    $result = '';

    switch ($status) {
        case 1:
            $result = "added";
            break;
        case -1:
            $result = "removed";
            break;
    }

    return $result;
}
