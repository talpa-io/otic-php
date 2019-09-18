<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 18.09.19
 * Time: 14:31
 */

namespace OticTest;


use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

class FormatTest extends TestCase
{


    public function testUnitConversion()
    {

        $this->assertTrue(true === is_numeric("1e9"));
        $this->assertTrue(false === is_numeric("25.29s"));

        $this->assertEquals(148, (int)"1.48e2");
    }

}
