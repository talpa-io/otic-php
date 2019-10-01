<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 01.10.19
 * Time: 15:18
 */

namespace Otic\mw;


use Otic\AbstractOticMiddleware;

class InduradMiddleware extends AbstractOticMiddleware {

    private $failOnErr = true;

    private $minTs;

    public function __construct()
    {
        $this->minTs = strtotime("2016-01-01 00:00:00");
    }

    public function message(array $data)
    {
        if ( ! is_array($data) || count($data) !== 5 ) {
            if ($this->failOnErr)
                throw new \InvalidArgumentException("Line malformed: " . print_r($data, true));
            phore_log()->warn("Ignoring line " . print_r ($data, true));
            return;
        }

        $timestamp = $data[0];
        if ($timestamp < $this->minTs) {
            if ($this->failOnErr)
                throw new \InvalidArgumentException("Line malformed: " . print_r($data, true));
            phore_log()->warn("Timestamp $timestamp before 2018");
            return;
        }
        $colName = $data[1];
        $mu = $data[3];
        $value = $data[4];

        $this->next->message(["ts"=>$timestamp, "colname"=>$colName, "value"=>$value, "metadata" => $mu]);
    }
}
