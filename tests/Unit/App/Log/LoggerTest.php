<?php

namespace App\Log;


use PHPUnit\Framework\TestCase;

$mockDate = false;

function date($f, $timestamp = null)
{
    global $mockDate;
    if ($mockDate) {
        return '2017-12-31 11:11:29';
    } else {
        return \date($f, $timestamp);
    }
}

class LoggerTest extends TestCase
{

    public static $path = __DIR__ . '/../../../storage';

    public static function getFile()
    {
        return self::$path . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
    }

    /**
     * @before
     */
    public function setUp()
    {
        global $mockDate;
        $mockDate = true;
        if (file_exists(self::getFile())) {
            unlink(self::getFile());
        }
    }

    /**
     * @after
     */
    public function tearDown()
    {
        global $mockDate;
        if (file_exists(self::getFile())) {
            unlink(self::getFile());
        }

        $mockDate = false;
    }

    public function testWrite()
    {
        $log = new Logger(self::$path);
        $log->write('FOO');
        $expected = "[2017-12-31 11:11:29][] FOO\n";
        $actual   = file_get_contents(self::getFile());
        $this->assertEquals($expected, $actual);
    }

    public function testWriteWithType()
    {
        $log = new Logger(self::$path);
        $log->write('FOO','FOOTYPE');
        $expected = "[2017-12-31 11:11:29][FOOTYPE] FOO\n";
        $actual   = file_get_contents(self::getFile());
        $this->assertEquals($expected, $actual);
    }
}