<?php

namespace Gendiff;

use function Gendiff\Parsers\getJsonData;
use function Gendiff\Parsers\getYamlData;
use function Gendiff\Utils\getExFromFile;
use function Gendiff\Formatters\getGitFormat;
use function Gendiff\Formatters\getPlainFormat;
use function Gendiff\Formatters\getJSONformat;

function buildDiff(array $original, array $committed): array
{
    $merged = array_merge($original, $committed);
    $changes = array_reduce(
        array_keys($merged),
        function ($acc, $key) use ($original, $committed) {
            $status = array_key_exists($key, $committed) <=> array_key_exists($key, $original);
            if ($status === 0) {
                if ($original[$key] === $committed[$key]) {
                    $acc[] = [["{$key}" => $original[$key]], "status" => 'unchanged'];
                } else {
                    $acc[] = [["{$key}" => $original[$key]], 'status' => 'deleted'];
                    $acc[] = [["{$key}" => $committed[$key]], 'status' => 'added'];
                }
            }
            if ($status === 1) {
                $acc[] = [["{$key}" => $committed[$key]], 'status' => 'added'];
            }
            if ($status === -1) {
                $acc[] = [["{$key}" => $original[$key]], 'status' => 'deleted'];
            }
            return $acc;
        },
        []
    );
    usort($changes, fn ($a, $b) => array_keys($a[0]) <=> array_keys($b[0]));
    return array_values([...$changes]);
}

function buildData(string $path): array
{
    $extension = getExFromFile($path);
    $data = [];

    if ($extension === "json") {
        $data = getJsonData($path);
    }
    if ($extension === "yaml" || $extension === "yml") {
        $data = getYamlData($path);
    }
    return $data;
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = null): string
{
    $original = buildData($pathToFile1);
    $committed = buildData($pathToFile2);
    $changes = buildDiff($original, $committed);

    if ($format === "plain") {
        return getPlainFormat($changes);
    }
    if ($format === "JSON") {
        return getJSONformat($changes);
    }

    return getGitFormat($changes);
}