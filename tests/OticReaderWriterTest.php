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
use Otic\OticException;
use Otic\OticReader;
use Otic\OticWriter;
use PHPUnit\Framework\TestCase;

class OticReaderWriterTest extends TestCase
{
    private $writer;

    protected function setUp(): void
    {
        $this->writer = new OticWriter();
        $this->writer->open("/tmp/out.otic");
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
        $this->assertTrue(true);
    }

    public function testDescendingTimestamps() {
        $this->writer->inject(1582612585, "name", 132, "kpi");
        $this->expectException(LibOticException::class);
        $this->expectExceptionMessage("Invalid Timestamp");
        $this->writer->inject(1582612580, "name", 132, "kpi");
        $this->writer->close();
        $this->assertTrue(true);
    }

    public function testTimestampSmallFloat() {
        for($i=0;$i<10;$i++) {
            $n = 1582612585+$i;
            $ts = "$n." . pow(11,$i) . "7";
            $this->writer->inject($ts, "name", 132, "kpi");
        }
        $this->writer->close();
        $this->assertTrue(true);
    }

    public function testNamesWithRandomLength() {
        $names = [];
        for($i=0; $i<200; $i++) {
            $names[] = bin2hex(random_bytes(rand(30,123)));
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
//        print_r($this->writer->getStats());
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

    public function testWriteBoolean() {
        $this->writer->inject(1582612585, "name", true, "kpi");
        $this->writer->close();
        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $data = [];
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data) {
            $data =  [$timestamp, $colname, $unit, $value];
        });
        $reader->read();
        $this->assertTrue($data[3]);
    }

    public function testWriteNull() {
        $this->writer->inject(1582612585, "name", null, "kpi");
        $this->writer->close();
        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $data = [];
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data) {
            $data =  [$timestamp, $colname, $unit, $value];
        });
        $reader->read();
        $this->assertNull($data[3]);
    }

    public function testExceptionWriteArray() {
        $array = [123, "abc"];
        $this->expectException(OticException::class);
        $this->expectExceptionMessage("Unsupported Type");
        $this->writer->inject(1582612585, "name", $array, "kpi");
        $this->writer->close();
    }

    public function testExceptionWriteObject() {
        $object = new \DateTime();
        $this->expectException(OticException::class);
        $this->expectExceptionMessage("Unsupported Type");
        $this->writer->inject(1582612585, "name", $object, "kpi");
        $this->writer->close();
    }

    public function testExceptionWriteCallback() {
        $cbFun = function () { return 1; };
        $this->expectException(OticException::class);
        $this->expectExceptionMessage("Unsupported Type");
        $this->writer->inject(1582612585, "name", $cbFun, "kpi");
        $this->writer->close();
    }

    public function testExceptionWriteResource() {
        $file = fopen("/tmp/file", "w");
        $this->expectException(OticException::class);
        $this->expectExceptionMessage("Unsupported Type");
        $this->writer->inject(1582612585, "name", $file, "kpi");
        $this->writer->close();
    }

    public function testNamesContainsAscii0to255() {
        for($i = 0; $i<255; $i++) {
            $char = chr($i);
            $this->writer->inject(1582612585, $char, 123, "kpi");
        }
        $this->writer->close();
        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $data = [];
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data) {
            $data[] =  [$timestamp, ord($colname), $unit, $value];
        });
        $reader->read([chr(0), chr(1), chr(210)]);
        $this->assertEquals(0, $data[0][1]);
        $this->assertEquals(1, $data[1][1]);
        $this->assertEquals(210, $data[2][1]);
    }

    public function testSelectNonExistingColumn() {
        $this->writer->inject(1582612585, "name", 123, "kpi");
        $this->writer->close();

        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $data = [];
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data) {
            $data =  [$timestamp, $colname, $unit, $value];
        });
        $reader->read(["fail"]);
        $this->assertEquals([], $data);
    }

    public function testTimeIntervalEmptyFile() {
        $this->writer->close();

        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $dataSetsRead = 0;
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$dataSetsRead) {
            $dataSetsRead++;
        });
        $reader->read();
        $this->assertEquals(0, $dataSetsRead);
        $this->assertNull($reader->getFirstTimestamp());
        $this->assertNull($reader->getLastTimestamp());
    }


}
