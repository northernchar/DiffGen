<?php

namespace Gendiff;

use function Gendiff\Parsers\getJsonData;
use function Gendiff\Parsers\getYamlData;
use function Gendiff\Utils\isJson;
use function Gendiff\Utils\isYaml;
use function Gendiff\Utils\isAssoc;
use function Gendiff\Utils\getAst;
use function Gendiff\Utils\getValueType;
use function Gendiff\Formatters\format;

function buildDiff(array $original, array $committed): array
{
    $merged = array_merge($original, $committed);
    $changes = array_reduce(
        array_keys($merged),
        function ($acc, $key) use ($original, $committed) {
            $status = array_key_exists($key, $committed) <=> array_key_exists($key, $original);
            if ($status === 0) {
                if(isAssoc($original[$key]) && isAssoc($committed[$key])) {
                    $val = buildDiff($original[$key], $committed[$key]);
                    $acc[] = getAst($key, $val, 0);
                    return $acc;
                }
                if ($original[$key] === $committed[$key]) {
                    $acc[] = getAst($key, $original[$key], 0, getValueType($original[$key]));
                    return $acc;
                } else {
                    $re = [getAst($key, $original[$key], -1, getValueType($original[$key])), getAst($key, $committed[$key], 1, getValueType($committed[$key]))];
                    $acc[] = getAst($key, $re, 2);
                    return $acc;
                }
            }
            if ($status === 1) {
                $acc[] = getAst($key, $committed[$key], 1, getValueType($committed[$key]));
                return $acc;
            }
            if ($status === -1) {
                $acc[] = getAst($key, $original[$key], -1, getValueType($original[$key]));
                return $acc;
            }
            return $acc;
        },
        []
    );

    usort($changes, function ($a, $b) {
        $dataA = $a['data'];
        $dataB = $b['data'];

        return array_keys($dataA) <=> array_keys($dataB);
    });

    return array_values([...$changes]);
}

function buildData(string $path): array
{
    $data = [];

    if (isJson($path)) {
        $data = getJsonData($path);
    }
    if (isYaml($path)) {
        $data = getYamlData($path);
    }
    return $data;
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = "stylish"): string
{
    $original = buildData($pathToFile1);
    $committed = buildData($pathToFile2);
    $changes = buildDiff($original, $committed);

    return format($changes, $format);
}