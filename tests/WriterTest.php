<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 11:42
 */

namespace OticTest;

//engine__1_engine_turbocharger_1_compressor_intake_pressure

use Otic\LibOticException;
use Otic\OticReader;
use Otic\OticWriter;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    private $writer;

    protected function setUp(): void
    {
        $this->writer = new OticWriter();
        $this->writer->open("/tmp/out.otic");
    }

    protected function tearDown(): void
    {
//        $this->writer->close();
    }

    public function testExceptionMaxLenghtNameUnit() {
        $name = bin2hex(random_bytes(64));
        $unit = bin2hex(random_bytes(64));

        $this->expectException(LibOticException::class);
        $this->expectExceptionMessage("Buffer Overflow");

        $this->writer->inject(1582612585, $name, 123, $unit);
        $this->writer->close();
    }

    public function testExceptionValueTooBig() {
        $value = bin2hex(random_bytes(128));

        $this->expectException(LibOticException::class);
        $this->expectExceptionMessage("Buffer Overflow");

        $this->writer->inject(1582612585, "name", $value, "unit");
        $this->writer->close();
    }

    public function testWriteReadTsZero() {
        $this->writer->inject(0, "name", "value", "unit");
        $this->writer->close();

        $data = [];
        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data) {
            $data =  [$timestamp, $colname, $unit, $value];
        });
        $reader->read();
        $this->assertEquals([0.0,"name", "unit", "value"], $data);
    }

    public function testRandomNames() {
        for($i=0;$i<1000;$i++) {
            $name = bin2hex(random_bytes(rand(30, 90)));
            $this->writer->inject(1582612585, $name, 132, "kpi");
        }
        $this->writer->close();
    }

    public function testDescendingTimestamps() {
        $this->writer->inject(1582612585, "name", 132, "kpi");
        $this->expectException(LibOticException::class);
        $this->expectExceptionMessage("Invalid Timestamp");
        $this->writer->inject(1582612580, "name", 132, "kpi");
        $this->writer->close();
    }

    public function testTimestampSmallFloat() {
        for($i=0;$i<10;$i++) {
            $n = 1582612585+$i;
            $ts = "$n." . pow(11,$i) . "7";
            $this->writer->inject($ts, "name", 132, "kpi");
        }
        $this->writer->close();
    }

    public function testNamesWithRandomLength() {
        $names = [];
        for($i=0; $i<200; $i++) {
            $names[] = bin2hex(random_bytes(rand(30,61)));
        }

        $timestamp=1582612585.419277;
        for ($i=0; $i<864; $i++) {
            $timestamp+=1.123;
            for ($i2=0; $i2<200; $i2++) {
                $unit = "u$i2";
                $name = $names[$i2];
                $value = pow(13,rand(1,4)) * rand(0,99) . "." . rand(0,9999);
                $this->writer->inject($timestamp, $name, $value, $unit);
            }
        }
        $this->writer->close();

        $data = [];
        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data) {
            $data =  [$timestamp, $colname, $unit, $value];
        });
        $reader->read();

        $this->assertTrue(true);

    }

}
