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
        for ($i=0; $i<86400; $i++) {
            for ($i2=0; $i2<10; $i2++) {
                $writer->inject($i, "someName" . $i2, "moo" . ($i + $i2));
            }
        }
        phore_out("end writing");

        $writer->close();
        $this->assertTrue(true);
    }


    public function testBenchmarkReader()
    {
        $reader = new OticReader();
        $reader->open("/tmp/outbench.otic");
        phore_out("start reading");
        $reader->setOnDataCallback(function ($timestamp, $colname, $value) {
            //echo "\n$timestamp;$colname;$value";
        });

        $read = $reader->read(["someName1", "someName2", "someName3"]);
        phore_out("end reading ($read)");
        $this->assertTrue(true);
    }


}
