<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 01.10.19
 * Time: 15:38
 */

namespace Otic\mw;


use Otic\AbstractOticMiddleware;

class DefaultMiddleware extends AbstractOticMiddleware
{

    private $failOnErr = true;

    private $minTs;

    public function __construct()
    {
        $this->minTs = strtotime("2016-01-01 00:00:00");
    }

    public function message(array $data)
    {
        if ( ! is_array($data) || count($data) !== 4 ) {
            if ($this->failOnErr)
                throw new \InvalidArgumentException("Line malformed: " . print_r($data, true));
            phore_log()->warning("Ignoring line " . print_r ($data, true));
            return;
        }

        $timestamp = $data[0];
        if ($timestamp < $this->minTs) {
            if ($this->failOnErr)
                throw new \InvalidArgumentException("Line malformed: " . print_r($data, true));
            phore_log()->warning("Timestamp $timestamp before 2018");
            return;
        }
        $colName = $data[1];
        $metaData = $data[2];
        $value = $data[3];

        $this->next->message(["ts"=>$timestamp, "colname"=>$colName, "value"=>$value, "metadata" => $metaData]);
    }
}
