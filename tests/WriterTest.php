<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 11:42
 */

namespace OticTest;

//engine__1_engine_turbocharger_1_compressor_intake_pressure

use Otic\OticReader;
use Otic\OticWriter;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    public function WriterWritesData()
    {

        $writer = new OticWriter();
        $writer->open("/tmp/out.otic");

        $ts = "1582612585.419277";
        $name = "engine__1_engine_turbocharger_1_compressor_intake_pressure";
        $unit = "float";
        $value = "98.7500000000000000";

        $writer->inject($ts, $name, $value, $unit);

        $writer->close();


        $reader = new OticReader();
        $reader->open("/tmp/out.otic");
        $reader->setOnDataCallback(function ($timestamp, $colname, $value, $mu) {
//            echo "\n$timestamp;$colname;$value;$mu";
        });
        $reader->read();

    }

}
