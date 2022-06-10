<?php

namespace Gendiff;

use function Functional\if_else;

/**
 * Define template file.
 *
 * @param array $gitted must be a path to File1
 *
 * @return string
 */
function format($gitted)
{
    $mapped = array_map(fn ($item) => "  {$item['status']} {$item['key']}: {$item['value']}", $gitted);
    $formatted = implode("\n", $mapped);
    $result = "{\n{$formatted}\n}";
    return $result;
}

/**
 * Define template file.
 *
 * @param string $pathToFile must be a path to File1
 *
 * @return array
 */
function getJSONData($pathToFile)
{
    $json = file_get_contents($pathToFile);
    $rowData = json_decode($json, true);

    $data = array_map(
        function ($item) {
            if ($item === true) {
                return 'true';
            }

            if ($item === false) {
                return 'false';
            }

            return $item;
        },
        $rowData
    );

    return $data;
}

/**
 * Define template file.
 *
 * @param string $pathToFile1 must be a path to File1
 * @param string $pathToFile2 must be a path to File2
 * @param string $format      defines expected output format
 *
 * @return string
 */
function genDiff($pathToFile1, $pathToFile2, $format = null)
{
    $original = getJSONData($pathToFile1);
    $committed = getJSONData($pathToFile2);

    $changes = array_merge($original, $committed);

    $gitted = array_reduce(
        array_keys($changes),
        function ($acc, $key) use ($original, $committed) {
            $status = array_key_exists($key, $committed) <=> array_key_exists($key, $original);

            switch ($status) {
                case 0:
                    if ($original[$key] === $committed[$key]) {
                        $acc[] = ['key' => $key, 'value' => $original[$key], 'status' => ' '];
                    } else {
                        $acc[] = ['key' => $key, 'value' => $original[$key], 'status' => '-'];
                        $acc[] = ['key' => $key, 'value' => $committed[$key], 'status' => '+'];
                    }
                    break;
                case 1:
                    $acc[] = ['key' => $key, 'value' => $committed[$key], 'status' => '+'];
                    break;
                case -1:
                    $acc[] = ['key' => $key, 'value' => $original[$key], 'status' => '-'];
                    break;
            }
            return $acc;
        },
        []
    );

    usort($gitted, fn ($a, $b) => $a['key'] <=> $b['key']);

    return format($gitted);
}
