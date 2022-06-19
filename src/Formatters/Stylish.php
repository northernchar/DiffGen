<?php

namespace Gendiff\Formatters;

use function Gendiff\Utils\toString;
use function Gendiff\Utils\isAssoc;

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

function stylish($node, string $replacer = " ", $spacesCount = 2): string
{
    
    $iter = function ($node, $depth) use (&$iter, $replacer, $spacesCount) {
        if (!is_array($node)) {
            return toString($node);
        }
        
        $indentSize = $depth * $spacesCount;
        $currentIndent = str_repeat($replacer, $indentSize);
        $bracketIndent = str_repeat($replacer, $indentSize - $spacesCount);
        $openBracket = array_key_exists('type', $node)  && $node['type'] === 'array' ? "[" : "{";
        $closeBracket = array_key_exists('type', $node) && $node['type'] === 'array' ? "]" : "}";
        $isNode = (isAssoc($node) && array_key_exists('data', $node)) || (!isAssoc($node) && is_array($node) && array_key_exists('data', $node[0]));

        if (!$isNode && is_array($node)) {
            $currentIndent = str_repeat($replacer, $indentSize + $spacesCount);
            $lines = array_map(
                fn($key, $val) => "{$currentIndent}{$key}: {$iter($val, $depth + 2)}",
                array_keys($node),
                $node
            );

            $result = [$openBracket, ...$lines, "{$bracketIndent}{$closeBracket}"];
            return implode("\n", $result);
        }

        $lines = array_reduce($node, function ($acc, $item) use (&$iter, $currentIndent, $depth) {

            $data = $item['data'];
            $statusCode = $item['meta']['status'];
            $status = getStylishStatus($statusCode);
            $key = array_key_first($data);
            $val = $data[$key];
            if($statusCode === 2) {
                $removed = $val[0]['data'];
                $added = $val[1]['data'];
                $removedKey = array_key_first($removed);
                $addedKey = array_key_first($added);
                $removedVal = !is_array($removed[$removedKey]) ? toString($removed[$removedKey]) : $removed[$removedKey];
                $addedVal = !is_array($added[$addedKey]) ? toString($added[$addedKey]) : $added[$addedKey];
                $acc[] = !is_array($removedVal) ? "{$currentIndent}- {$removedKey}: {$removedVal}" : "{$currentIndent}- {$removedKey}: {$iter($removedVal, $depth + 2)}";
                $acc[] = !is_array($addedVal) ? "{$currentIndent}+ {$addedKey}: {$addedVal}" : "{$currentIndent}+ {$addedKey}: {$iter($addedVal, $depth + 2)}";
                return $acc;
            }
            $acc[] = "{$currentIndent}{$status} {$key}: {$iter($val, $depth + 2)}";
            return $acc;
        }, []);

        $result = [$openBracket, ...$lines, "{$bracketIndent}{$closeBracket}"];

        return implode("\n", $result);
    };

    return $iter($node, 1);
}