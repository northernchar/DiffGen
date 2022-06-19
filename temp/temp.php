<?php

namespace temp;

require_once "../vendor/autoload.php";

use function Gendiff\genDiff;
use function Gendiff\buildData;
use function Gendiff\buildDiff;

$path1 = "../tests/fixtures/json/file3.json";
$path2 = "../tests/fixtures/json/file4.json";

$original = buildData($path1);
$committed = buildData($path2);
$changes = buildDiff($original, $committed);


$result = genDiff($path1, $path2, "plain");

print_r($result);
