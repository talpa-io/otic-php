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
        for ($i=0; $i<8640; $i++) {
            $timestamp+=1.123;
            for ($i2=0; $i2<100; $i2++) {
                $unit = "u$i2";
                $name = "s$i2".$i%100;//bin2hex(random_bytes(rand(20,60)));
                $value = $i.$i2; //rand(0,999) . "." . rand(100000000000000,900000000000000);
                $writer->inject($timestamp, $name, $value, $unit);
            }
        }
        phore_out("end writing");

        $writer->close();
        $this->assertTrue(true);
    }

    public function testBenchmarkReader()
    {
        $data = [];
        $reader = new OticReader();
        $reader->open("/tmp/outbench.otic");
        phore_out("start reading");
        $reader->setOnDataCallback(function ($timestamp, $colname, $unit, $value) use (&$data) {
            $data[] = ['ts'=>$timestamp, 'name'=>$colname, 'unit'=>$unit, 'val'=>$value];
        });
        $reader->read();
        phore_out("end reading " . count($data));
        $this->assertTrue(true);
    }

}
