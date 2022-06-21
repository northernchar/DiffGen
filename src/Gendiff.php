<?php

namespace Differ\Differ;

use function Differ\Parsers\getJsonData;
use function Differ\Parsers\getYamlData;
use function Differ\Utils\isJson;
use function Differ\Utils\isYaml;
use function Differ\Utils\isAssoc;
use function Differ\Utils\getAst;
use function Differ\Utils\getValueType;
use function Differ\Formatters\format;
use function Functional\sort;

function buildDiff(array $original, array $committed): array
{
    $merged = array_merge($original, $committed);
    $changes = array_reduce(
        array_keys($merged),
        function ($acc, $key) use ($original, $committed) {
            $status = array_key_exists($key, $committed) <=> array_key_exists($key, $original);
            if ($status === 0) {
                if (isAssoc($original[$key]) && isAssoc($committed[$key])) {
                    $val = buildDiff($original[$key], $committed[$key]);
                    return [...$acc, getAst($key, $val, 0)];
                }
                if ($original[$key] === $committed[$key]) {
                    return [...$acc, getAst($key, $original[$key], 0, getValueType($original[$key]))];
                } else {
                    $re = [
                        getAst($key, $original[$key], -1, getValueType($original[$key])),
                        getAst($key, $committed[$key], 1, getValueType($committed[$key]))
                    ];
                    return [...$acc, getAst($key, $re, 2)];
                }
            }
            if ($status === 1) {
                return [...$acc, getAst($key, $committed[$key], 1, getValueType($committed[$key]))];
            }
            if ($status === -1) {
                return [...$acc, getAst($key, $original[$key], -1, getValueType($original[$key]))];
            }
        },
        []
    );

    $sortedChanges = sort($changes, function ($a, $b) {
        $dataA = $a['data'];
        $dataB = $b['data'];

        return array_keys($dataA) <=> array_keys($dataB);
    });

    return array_values([...$sortedChanges]);
}

function buildData(string $path): array
{
    if (isJson($path)) {
        return getJsonData($path);
    }
    if (isYaml($path)) {
        return getYamlData($path);
    }
    return [];
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $original = buildData($pathToFile1);
    $committed = buildData($pathToFile2);
    $changes = buildDiff($original, $committed);

    return format($changes, $format);
}
