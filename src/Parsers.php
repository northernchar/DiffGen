<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getJsonData(string $pathToFile): mixed
{
    $content = file_get_contents($pathToFile);
    return $content !== false ? json_decode($content, true) : '';
}

function getYamlData(string $pathToFile): mixed
{
    $content = Yaml::parseFile($pathToFile, Yaml::PARSE_OBJECT_FOR_MAP);
    $jsonified = json_encode($content);
    $preres = $jsonified !== false ? json_decode($jsonified, true) : '';
    return $preres;
}
