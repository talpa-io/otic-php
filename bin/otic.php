#!/usr/bin/php
<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.09.18
 * Time: 12:47
 */

namespace App;


use Otic\mw\DefaultMiddleware;
use Otic\OticConfig;

use InvalidArgumentException;

use Otic\OticReader;
use Otic\OticWriter;
use Phore\Cli\CliController;
use Phore\FileSystem\FileStream;
use Phore\FileSystem\PhoreTempFile;
use Phore\Log\Logger\PhoreEchoLoggerDriver;
use Phore\Log\PhoreLogger;


if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    require __DIR__ . "/../vendor/autoload.php";
} else {
    require __DIR__ . "/../../../autoload.php";
}

$group = CliController::GetInstance()->group("otic");


function packData(FileStream $in, string $out, bool $failOnErr, bool $indurad5colQuickfix, callable $onFileReady = null)
{
    $writer = $writer = new OticWriter();

    $writer->open($out);

    PhoreLogger::Init(new PhoreEchoLoggerDriver());
    $firstTs = null;
    $middleWareSource = OticConfig::GetWriterMiddleWareSource();
    if ($middleWareSource !== null) {
        $middleWareDrain = OticConfig::GetWriterMiddleWareDrain();
    } else {
        $middleWareSource = $middleWareDrain = new DefaultMiddleware();
    }
    $middleWareDrain->setNext($writer);


    while (!$in->feof()) {
        $data = $in->freadcsv(0, "\t");
        if ($data === null) {
            continue;
        }
        if (count($data) === 0) {
            continue;
        }


        //phore_log("Ts: {$timestamp} ColName: $colName Measure: {$mu} Value: {$value}");
        $middleWareSource->message($data);
    }
    $writer->close();
    $in->fclose();

    phore_log()->info("OK Imported " . date("Y-m-d H:i:s", $firstTs) . " - ");
    if ($onFileReady) {
        $onFileReady();
    }
}


$group->command("pack")
    ->withString("input", "the input file")
    ->withString("autoload", "php file to load additional middleware (otherwise doc/middleware/default_middleware is loaded)", __DIR__ . "/../doc/middleware/default_middleware.php")
    ->withBool("stdin", "read from strdin")
    ->withBool("stdout", "send data to stdout")
    ->withBool("indurad5colQuickfix", "Quick fix to fix indurad 5 column format")
    ->withBool("failOnErr", "Fail hard on input error (testing)")
    ->withString("afterCmd",
        "Run this script after each file compleded (Replace %f with converted filename, %if name of input file)")
    ->withString("out", "output file")
    ->run(function($input, bool $stdin, bool $indurad5colQuickfix, bool $failOnErr, string $out=null, bool $stdout=false, string $afterCmd=null, string $autoload=null) {

        $inFiles = null;
        if ($stdin) {
            $in = phore_file("php://stdin")->asFile()->fopen("r");
        } else {
            $inFiles = glob($input);
            //$in = phore_file($input)->assertFile()->fopen("r");
        }

        if ($autoload !== null) {
            require $autoload;
        }


        if ($out !== null) {
            $out = phore_file($out);
        } else {
            $out = new PhoreTempFile("otic-");
        }

        if ($inFiles !== null) {
            foreach ($inFiles as $inFile) {

                $outTmpFile = $out;

                phore_out("Start converting file $inFile... (tmp file)");
                $in = phore_file($inFile)->fopen("r");


                packData($in, $outTmpFile, $failOnErr, $indurad5colQuickfix,
                    function () use ($afterCmd, $outTmpFile, $inFile) {
                        phore_log("Done");
                        $afterCmd = str_replace("%f", (string)$outTmpFile, $afterCmd);
                        $afterCmd = str_replace("%if", (string)$inFile, $afterCmd);
                        if ($afterCmd !== "") {
                            phore_out("Running afterCmd: '$afterCmd'...");
                            phore_exec($afterCmd);
                            phore_out("Done (afterCmd)");
                        }

                    });
            }
        } else {
            if ($out === null) {
                throw new InvalidArgumentException("No output defined.");
            }
            packData($in, $out, $failOnErr, $indurad5colQuickfix);
            if ($stdout) {
                echo $out->get_contents();
            }
        }


    });


$group->command("unpack")
    ->withString("input", "the tbf input file")
    ->withBool("stdout", "Write to stdout")
    ->withString("out", "Output file")
    ->withString("include", "Include coloums", null)
    ->run(function ($input, $out, bool $stdout, $include = null) {
        if ($stdout) {
            $out = phore_file("php://stdout")->fopen("w");
        } else {
            $out = phore_file($out)->fopen("w");
        }


        $incCols = null;
        if ($include !== null) {
            $incCols = explode(";", $include);
        }

        $reader = new OticReader();
        $reader->open($input);
        $reader->setOnDataCallback(function ($ts, $colName, $value, $measure) use ($out) {
            $out->fputcsv([$ts, $colName, $measure, $value], "\t");
        });
        $reader->read($incCols);
        $out->fclose();
    });

CliController::GetInstance()->dispatch();


