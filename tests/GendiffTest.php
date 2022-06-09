<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\genDiff;

class GendiffTest extends TestCase
{
    public function testGendiff()
    {
        //Arrange
        $expected = <<<END
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        END;

        $path1 = "./src/file1.json";
        $path2 = "./src/file2.json";

        //Act
        $actual = genDiff($path1, $path2);
        //
        $this->assertEquals($expected, $actual);
    }
}
