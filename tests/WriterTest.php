<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 11:42
 */

namespace OticTest;



use Otic\OticReader;
use Otic\OticWriter;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{


    public function testWriterWritesData()
    {

        $writer = new OticWriter();
        $writer->open("/tmp/out.otic");

        $writer->inject(1234, "someName", 1234, "someUnit");

        $writer->close();


        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $reader->setOnDataCallback(function ($timestamp, $colname, $value, $mu) {
            echo "\n$timestamp;$colname;$value;$mu";
        });
        $reader->read();


    }

}
