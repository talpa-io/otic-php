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
        $count=0;
        for ($i=0; $i<8640; $i++) {
            for ($i2=0; $i2<100; $i2++) {
                $count++;
                $writer->inject($count, "someName" . $i2, "v$i.$i2", "string");
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
            $data = ['ts'=>$timestamp, 'name'=>$colname, 'unit'=>$unit, 'val'=>$value];
        });

        $reader->read();
        phore_out("end reading " . count($data));
        $this->assertTrue(true);
    }

//    public function testBenchmarkReadGenerator() {
//        $data = [];
//        $reader = new OticReader();
//        $reader->open("/tmp/outbench.otic");
//        phore_out("start reading generator");
//        foreach ($reader->readGenerator() as $line) {
//            $data[] = $line;
//        }
//        phore_out("end reading generator: " . count($data));
//        $this->assertTrue(true);
//    }

//    public function testBenchmarkReadGenerator2() {
//        $data = [];
//        $reader = new OticReader();
//        $reader->open("/tmp/outbench.otic");
//        phore_out("start reading generator2:\n");
//        foreach ($reader->generate() as $line) {
//            $data[] = $line;
//        }
//        phore_out("end reading generator2: " . count($data) ."\n");
//        $this->assertTrue(true);
//    }


}
