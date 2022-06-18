<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function getJsonData(string $pathToFile): array
{
    $content = file_get_contents($pathToFile);
    return json_decode($content, true);
}

function getYamlData(string $pathToFile): array
{
    $content = Yaml::parseFile($pathToFile, Yaml::PARSE_OBJECT_FOR_MAP);
    return json_decode(json_encode($content), true);
}

