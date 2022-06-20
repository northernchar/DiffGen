<?php

namespace Gendiff\Formatters;

use function Gendiff\Formatters\plain;
use function Gendiff\Formatters\stylish;
use function Gendiff\Formatters\json;

function format($collection, $format)
{
    if ($format === "plain") {
        return plain($collection);
    }
    if ($format === "json") {
        return json($collection);
    }

    return stylish($collection);
}
