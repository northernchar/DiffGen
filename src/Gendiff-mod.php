<?php

namespace Gendiff;

use function Functional\if_else;

function format($gitted)
{
    $mapped = array_map(fn($item) => "  {$item['status']} {$item['key']}: {$item['value']}", $gitted);
    $formatted = implode("\n", $mapped);
    $result = "{\n{$formatted}\n}";
    return $result;
}

function genDiff($pathToFile1, $pathToFile2, $format = null)
{
    $json_data1 = file_get_contents($pathToFile1);
    $json_data2 = file_get_contents($pathToFile2);

    $PREoriginal = json_decode($json_data1, true);
    $PREcommitted = json_decode($json_data2, true);


    $original = array_map(function($item) {
        if ($item === true) {
            return 'true';
        }
        if ($item === false) {
            return 'false';
        }
        return $item;
    }, $PREoriginal);

    $committed = array_map(function($item) {
        if ($item === true) {
            return 'true';
        }
        if ($item === false) {
            return 'false';
        }
        return $item;
    }, $PREcommitted);

    $changes = array_merge($original, $committed);

    $gitted = array_reduce(array_keys($changes), function ($acc, $key) use ($original, $committed) {
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
    }, []);

    usort($gitted, fn ($a, $b) => $a['key'] <=> $b['key']);

    return format($gitted);
}
