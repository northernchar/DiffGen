<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\genDiff;
use function Gendiff\format;
use function Gendiff\getChanges;
use function Gendiff\Parsers\getJSONData;

class GendiffTest extends TestCase
{
    /**
     * @covers Gendiff\getJSONData
     */
    public function testGetJSONData()
    {
        //Arrange
        $path1 = "./tests/fixtures/file1.json";
        $path2 = "./tests/fixtures/file2.json";

        $expected1 = [
          "host" => "hexlet.io",
          "timeout" => 50,
          "proxy" => "123.234.53.22",
          "follow" => "false"
        ];
        $expected2 = [
          "timeout" => 20,
          "verbose" => "true",
          "host" => "hexlet.io"
        ];

        //Act
        $actual1 = getJSONData($path1);
        $actual2 = getJSONData($path2);
        //Assert
        $this->assertEquals($expected1, $actual1);
        $this->assertEquals($expected2, $actual2);
    }

    /**
     * @covers Gendiff\getChanges
     */
    public function testGetChanges()
    {
      // Arrange
      $path1 = "./tests/fixtures/file1.json";
      $path2 = "./tests/fixtures/file2.json";
      $original = getJSONData($path1);
      $committed = getJSONData($path2);

      $expected = [
        ["key" => "follow", "value" => "false", "status" => "-"],
        ["key" => "host", "value" => "hexlet.io", "status" => " "],
        ["key" => "proxy", "value" => "123.234.53.22", "status" => "-"],
        ["key" => "timeout", "value" => 50, "status" => "-"],
        ["key" => "timeout", "value" => 20, "status" => "+"],
        ["key" => "verbose", "value" => "true", "status" => "+"]
      ];

      //Act
      $actual = getChanges($original, $committed);

      //Assert
      $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Gendiff\format
     */
    public function testformat()
    {
        //Arrange
        $expected = <<<END
        {
            host: hexlet.io
          - timeout: 50
          + timeout: 20
          - proxy: 123.234.53.22
          - follow: false
          + verbose: true
        }
        END;

        $collection = [
          ["key" => "host", "value" => "hexlet.io", "status" => " "],
          ["key" => "timeout", "value" => 50, "status" => "-"],
          ["key" => "timeout", "value" => 20, "status" => "+"],
          ["key" => "proxy", "value" => "123.234.53.22", "status" => "-"],
          ["key" => "follow", "value" => "false", "status" => "-"],
          ["key" => "verbose", "value" => "true", "status" => "+"]
        ];
        //Act
        $actual = format($collection);
        //Assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Gendiff\genDiff
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
