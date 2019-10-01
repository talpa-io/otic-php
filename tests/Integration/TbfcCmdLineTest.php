<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 29.10.18
 * Time: 12:23
 */

namespace Talpa\BinFmt\Test\Integration;


use PHPUnit\Framework\TestCase;

class TbfcCmdLineTest extends TestCase
{

    private $MOCK_4COL_BASIC_DATA = __DIR__ . "/mock/in_csv_4col.csv";
    private $MOCK_5COL_BASIC_DATA = __DIR__ . "/mock/in_csv_5col.csv";
    private $MOCK_REAL_DATA = __DIR__ . "/mock/test_real_data.txt";

    public function testTbfcDefaultPack()
    {
        phore_exec("/opt/bin/otic.php --otic --pack --failOnErr --input=$this->MOCK_4COL_BASIC_DATA --out=/tmp/out.tbfc");
        phore_exec("/opt/bin/otic.php --otic --unpack --input=/tmp/out.tbfc --out=/tmp/out_compare.csv");

        $this->assertFileEquals($this->MOCK_4COL_BASIC_DATA, "/tmp/out_compare.csv");
    }

    /**
     * Test the hotfix for Indurad 5 col output format generates the same
     * output like the 4 col input format. (4 col)
     *
     * @throws \Exception
     */
    public function testTbfcInduradHotFixPack()
    {
        phore_exec("/opt/bin/otic.php --otic --pack --autoload=/opt/doc/middleware/indurad_middleware.php --failOnErr --input=$this->MOCK_5COL_BASIC_DATA --out=/tmp/out.tbfc");
        phore_exec("/opt/bin/otic.php --otic --unpack --input=/tmp/out.tbfc --out=/tmp/out_compare.csv");

        $this->assertFileEquals($this->MOCK_4COL_BASIC_DATA, "/tmp/out_compare.csv");
    }

    /*
    public function testPackRealData()
    {
        phore_exec("/opt/bin/otic.php --otic --pack --indurad5colQuickfix --failOnErr --input=$this->MOCK_REAL_DATA --out=/tmp/out.tbfc");
        phore_exec("/opt/bin/otic.php --otic --unpack --input=/tmp/out.tbfc --out=/tmp/out_compare2.csv");

        $this->assertFileEquals($this->MOCK_REAL_DATA, "/tmp/out_compare2.csv");
    }
    */


    public function testAfterCmd()
    {

        phore_exec("bin/otic.php --otic --pack --failOnErr --input=$this->MOCK_4COL_BASIC_DATA --afterCmd='cat %f > /tmp/out_compare3.bin' --out=/tmp/out3.otic");
        $this->assertFileExists("/tmp/out_compare3.bin");

    }


}
