#!/usr/bin/php
<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.09.18
 * Time: 12:47
 */

namespace App;

use Otic\OticReader;
use Otic\OticWriter;
use Phore\Cli\CliController;
use Phore\FileSystem\FileStream;
use Phore\FileSystem\PhoreTempFile;
use Phore\Log\Logger\PhoreEchoLogger;
use Phore\Log\PhoreLog;


if (file_exists(__DIR__ . "/../vendor/autoload.php"))
    require __DIR__ . "/../vendor/autoload.php";
else
    require __DIR__ . "/../../../autoload.php";

$group = \Phore\Cli\CliController::GetInstance()->group("otic");




function packData(FileStream $in, string $out, bool $failOnErr, bool $indurad5colQuickfix, callable $onFileReady=null) {
    $writer = $writer = new OticWriter();

    $writer->open($out);

    PhoreLog::Init(new PhoreEchoLogger());
    $minTime = strtotime("2018-01-01 00:00:00");
    $firstTs = null;
    while ( ! $in->feof()) {
        $data = $in->freadcsv(0, "\t");
        if ($data === null)
            continue;
        if (count($data) === 0)
            continue;

        if ($indurad5colQuickfix) {
            if ( ! is_array($data) || count($data) !== 5 ) {
                if ($failOnErr)
                    throw new \InvalidArgumentException("Line malformed: " . print_r($data, true));
                phore_log()->warn("Ignoring line " . print_r ($data, true));
                continue;
            }
        } else {
            if ( ! is_array($data) || count($data) !== 4 ) {
                if ($failOnErr)
                    throw new \InvalidArgumentException("Line malformed: " . print_r($data, true));
                phore_log()->warn("Ignoring line " . print_r ($data, true));
                continue;
            }
        }



        $timestamp = $data[0];
        if ($timestamp < $minTime) {
            if ($failOnErr)
                throw new \InvalidArgumentException("Line malformed: " . print_r($data, true));
            phore_log()->warn("Timestamp $timestamp before 2018");
            continue;
        }
        $colName = $data[1];
        $mu = $data[2];
        $value = $data[3];
        if ($indurad5colQuickfix) {
            $mu = $data[3];
            $value = $data[4];
        }

        if ($firstTs === null)
            $firstTs = $timestamp;
        //phore_log("Ts: {$timestamp} ColName: $colName Measure: {$mu} Value: {$value}");
        $writer->inject($timestamp, $colName, $value, $mu);
    }
    $writer->close();
    $in->fclose();

    phore_log()->info("OK Imported " . date("Y-m-d H:i:s", $firstTs) . " - " . date("Y-m-d H:i:s", @$timestamp));
    if ($onFileReady)
        $onFileReady();
}


$group->command("pack")
    ->withString("input", "the input file")
    ->withBool("stdin", "read from strdin")
    ->withBool("stdout", "send data to stdout")
    ->withBool("indurad5colQuickfix", "Quick fix to fix indurad 5 column format")
    ->withBool("failOnErr", "Fail hard on input error (testing)")
    ->withString("afterCmd", "Run this script after each file compleded (Replace %f with converted filename, %if name of input file)")
    ->withString("out", "output file")
    ->run(function($input, bool $stdin, bool $indurad5colQuickfix, bool $failOnErr, string $out=null, bool $stdout=false, string $afterCmd=null) {
        $inFiles = null;
        if ($stdin) {
            $in = phore_file("php://stdin")->asFile()->fopen("r");
        } else {
            $inFiles = glob($input);
            //$in = phore_file($input)->assertFile()->fopen("r");
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



                packData($in, $outTmpFile, $failOnErr, $indurad5colQuickfix, function () use ($afterCmd, $outTmpFile, $inFile) {
                    phore_log("Done");
                    $afterCmd = str_replace("%f", (string) $outTmpFile, $afterCmd);
                    $afterCmd = str_replace("%if", (string) $inFile, $afterCmd);
                    if ($afterCmd !== "") {
                        phore_out("Running afterCmd: '$afterCmd'...");
                        phore_exec($afterCmd);
                        phore_out("Done (afterCmd)");
                    }

                });
            }
        } else {
            if ($out === null) {
                throw new \InvalidArgumentException("No output defined.");
            }
            packData($in, $out, $failOnErr, $indurad5colQuickfix);
            if ($stdout)
                echo $out->get_contents();
        }



    });


$group->command("unpack")
    ->withString("input", "the tbf input file")
    ->withBool("stdout", "Write to stdout")
    ->withString("out", "Output file")
    ->withString("include", "Include coloums", null)
    ->run (function ($input, $out, bool $stdout, $include=null) {
        if ($stdout) {
            $out = phore_file("php://stdout")->fopen("w");
        } else {
            $out = phore_file($out)->fopen("w");
        }


        $incCols = null;
        if ($include !== null)
            $incCols = explode(";", $include);

        $reader = new OticReader();
        $reader->open($input);
        $reader->setOnDataCallback(function ($ts, $colName, $value, $measure) use ($out) {
            $out->fputcsv([$ts, $colName, $measure, $value], "\t");
        });
        $reader->read($incCols);
        $out->fclose();
    });

CliController::GetInstance()->dispatch();


