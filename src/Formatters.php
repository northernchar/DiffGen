<?php

namespace Gendiff\Formatters;

use function Gendiff\Utils\toString;
use function Gendiff\Utils\isAssoc;
use function Gendiff\Utils\getStylishStatus;
use function Functional\flatten;

function plain($node)
{
    $iter = function ($node, $path) use (&$iter) {
        
        $changes = array_reduce($node, function($acc, $item) use ($path) {
            $data = $item["data"];
            $statusCode = $item['meta']['status'];
            $status = getStylishStatus($statusCode);
            $key = array_key_first($data);
            $currentPath = $path !== '' ? "{$path}.{$key}" : "{$key}";

        }, []);
        $flattened = flatten($changes);
        $resukt = implode("\n", $flattened);
    };

    return $iter($node, '');
}

function json($node)
{
    
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
            $acc[] = "{$currentIndent}{$status} {$key}: {$iter($val, $depth + 2)}";
            return $acc;
        }, []);

        $result = [$openBracket, ...$lines, "{$bracketIndent}{$closeBracket}"];

        return implode("\n", $result);
    };

    return $iter($node, 1);
}
