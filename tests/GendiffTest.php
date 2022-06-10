<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\genDiff;

class GendiffTest extends TestCase
{
    /**
     * Test Gendiff
     * 
     * @covers Gendiff\genDiff
     * 
     * @return void
     */
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

        $path1 = "./tests/fixtures/file1.json";
        $path2 = "./tests/fixtures/file2.json";

        //Act
        $actual = genDiff($path1, $path2);
        //Assert
        $this->assertEquals($expected, $actual);
    }
}
