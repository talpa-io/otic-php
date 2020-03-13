<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 13:53
 */

namespace OticTest;


use Otic\OticReader;
use Otic\OticWriter;
use PHPUnit\Framework\TestCase;

/**
 * Class BenchmarkTest
 * @package OticTest
 * @internal
 * @skip
 */
class BenchmarkTest extends TestCase
{

    public function testBenchmarkWriter()
    {
        $writer = new OticWriter();
        $writer->open("/tmp/outbench.otic");

        phore_out("start writing");
        $timestamp=1582612585.419277;
        for ($i=0; $i<86400; $i++) {
            $timestamp+=1.123;
            for ($i2=0; $i2<120; $i2++) {
                $unit = "u$i2";
                $name = "s$i2".$i%100;//bin2hex(random_bytes(rand(20,60)));
                $value = $i.$i2; //rand(0,999) . "." . rand(100000000000000,900000000000000);
                $writer->inject($timestamp, $name, $value, $unit);
            }
        }
        phore_out("end writing (10,368,000 lines) ");

        $writer->close();
        $this->assertTrue(true);
    }

    public function testBenchmarkReadAll()
    {
        $data = [];
        $count = 0;
        $reader = new OticReader();
        $reader->open("/tmp/outbench.otic");
        phore_out("start reading all data");
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data, &$count) {
            $data = ['ts'=>$timestamp, 'name'=>$colname, 'unit'=>$unit, 'val'=>$value];
            $count++;
        });
        $reader->read();
        phore_out("end reading ($count lines)");
        $this->assertTrue(true);
    }

    public function testBenchmarkReadSelection()
    {
        $data = [];
        $count = 0;
        $reader = new OticReader();
        $reader->open("/tmp/outbench.otic");
        phore_out("start reading two columns");
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data, &$count) {
            $data = ['ts'=>$timestamp, 'name'=>$colname, 'unit'=>$unit, 'val'=>$value];
            $count++;
        });
        $reader->read(["s120","s121"]);
        phore_out("end reading ($count lines)");
        $this->assertTrue(true);
    }


}
