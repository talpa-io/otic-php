<?php


use Otic\OticWriter;

if (file_exists(__DIR__ . "/../vendor/autoload.php")) {
    require __DIR__ . "/../vendor/autoload.php";
} else {
    require __DIR__ . "/../../../autoload.php";
}

testRandomBytes();
function testRandomBytes() {
    echo __FUNCTION__."\n";
    $names = [];
    for($i=0;$i<100;$i++) {
        try {
            $names[] = bin2hex(random_bytes($i));
        } catch (\Error $e) {
            echo "error:" . $e->getMessage()."\n";
        } catch (Exception $err) {
            echo "exception:" . $err->getMessage()."\n";
        }
    }
    if(count($names)===99) {
        echo "test successful\n";
    } else {
        echo "test failed\n";
    }
    echo "\n";
}

//testSegfaultWhenOticWriterInitializedAndUnhandledErrorOccurs();
function testSegfaultWhenOticWriterInitializedAndUnhandledErrorOccurs() {
    echo __FUNCTION__."\n";
    $writer = new OticWriter();
    $writer->open("/tmp/out.otic");
    $names = bin2hex(random_bytes(0));
    $writer->close();
    echo "\n";
}

//testSegfaultWhenWritingDifferentLengthNames();
function testSegfaultWhenWritingDifferentLengthNames() {
    echo __FUNCTION__."\n";
    $writer = new OticWriter();
    $writer->open("/tmp/out.otic");
    $pow = "";
    for($i=1;$i<100;$i++) {
        $pow .= pow(10,$i);
        echo "i: $i, length: " . strlen($pow) .", $pow\n";
        $writer->inject(1582612585, $pow, 132, "kpi");
    }
    $writer->close();
}

//testMemoryCorruptionWhenWritingRandomNamesWithIncreasingLength();
function testMemoryCorruptionWhenWritingRandomNamesWithIncreasingLength() {
    echo __FUNCTION__."\n";
    $writer = new OticWriter();
    $writer->open("/tmp/out.otic");
    $names = [];
    for($i=0;$i<100;$i++) {
        try {
            $names[] = bin2hex(random_bytes($i));
        } catch (\Error $e) {
            echo "error:" . $e->getMessage()."\n";
        } catch (Exception $err) {
            echo "exception:" . $err->getMessage()."\n";
        }
    }
    foreach ($names as $i => $name) {
        echo $i . ": " . $name . "\n";
        $writer->inject(1582612585, $name, 132, "kpi");
    }
    $writer->close();
}

//testMemoryCorruptionWhenWritingRandomNamesWithPrefixAndIncreasingLength();
function testMemoryCorruptionWhenWritingRandomNamesWithPrefixAndIncreasingLength() {
    echo __FUNCTION__."\n";
    $writer = new OticWriter();
    $writer->open("/tmp/out.otic");
    $names = [];
    for($i=1;$i<100;$i++) {
        try {
            $names[] = "prefix" . bin2hex(random_bytes($i));
        } catch (\Error $e) {
            echo "error:" . $e->getMessage()."\n";
        } catch (Exception $err) {
            echo "exception:" . $err->getMessage()."\n";
        }
    }
    foreach ($names as $i => $name) {
        echo $i . ": " . $name . "\n";
        $writer->inject(1582612585, $name, 132, "kpi");
    }
    if(count($names)===99) {
        echo "\ntest successful\n";
    } else {
        echo "\ntest failed\n";
    }
    echo "\n";
    $writer->close();
}

//testMemoryCorruptionWhenWritingRandomNamesWithPrefixAndRandomLength();
function testMemoryCorruptionWhenWritingRandomNamesWithPrefixAndRandomLength() {
    echo __FUNCTION__."\n";
    $writer = new OticWriter();
    $writer->open("/tmp/out.otic");
    $names = [];
    for($i=1;$i<260;$i++) {
        try {
            $names[] = "prefix" . bin2hex(random_bytes(rand(10,30)));
        } catch (\Error $e) {
            echo "error:" . $e->getMessage()."\n";
        } catch (Exception $err) {
            echo "exception:" . $err->getMessage()."\n";
        }
    }
    foreach ($names as $i => $name) {
        echo $i . ": " . $name . "\n";
        $writer->inject(1582612585, $name, 132, "kpi");
    }
    if(count($names)===99) {
        echo "\ntest successful\n";
    } else {
        echo "\ntest failed\n";
    }
    echo "\n";
    $writer->close();
}
