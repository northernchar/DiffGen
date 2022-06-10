<?php

namespace Gendiff;

function format(array $changes): string
{
    $mapped = array_map(fn ($item) => "  {$item['status']} {$item['key']}: {$item['value']}", $changes);
    $formatted = implode("\n", $mapped);
    $result = "{\n{$formatted}\n}";
    return $result;
}

function getJSONData(string $pathToFile): array
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

function getChanges(array $merged, array $original, array $committed): array
{
    $changes = array_reduce(
        array_keys($merged),
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
    return $changes;
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = null): string
{
    $original = getJSONData($pathToFile1);
    $committed = getJSONData($pathToFile2);
    $merged = array_merge($original, $committed);
    $changes = getChanges($merged, $original, $committed);

    usort($changes, fn ($a, $b) => $a['key'] <=> $b['key']);
    $result = format($changes);
    return $result;
}
