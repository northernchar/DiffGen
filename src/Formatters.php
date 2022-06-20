<?php

namespace Differ\Formatters;

use function Differ\Formatters\plain;
use function Differ\Formatters\stylish;
use function Differ\Formatters\json;

function format(array $collection, string $format)
{
    if ($format === "plain") {
        return plain($collection);
    }
    if ($format === "json") {
        return json($collection);
    }

    return stylish($collection);
}
