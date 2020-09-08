<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 11:25
 */

namespace Otic;


class OticReader extends OticBase
{

    private $reader;

    private $callback;

    private $firstTimestamp = null;
    private $lastTimestamp = null;

    public $datasetsRead = 0;

    public function open (string $filename)
    {
        $this->reader = new \UrdtsfmtReader();
        $this->reader->open($filename);

    }


    public function setOnDataCallback(callable $cb)
    {
        $this->callback = $cb;
    }


    public function getFirstTimestamp() : ?float
    {
        return $this->firstTimestamp;
    }

    public function getLastTimestamp() : ?float
    {
        if ($this->reader !== null)
            throw new \InvalidArgumentException("last timestamp is only available after read() has completed.");
        return $this->lastTimestamp;
    }


    public function read(array $cols = null) : int
    {

        $index = 0;
        while($data = $this->reader->read()) {
            $colname = $data["colname"];
            $mu = $data["metadata"];

            if ($cols !== null && ! in_array($colname, $cols)) {
                $this->reader->ignore_previous_column();
                continue;
            }
            $index++;

            if ($this->firstTimestamp === null)
                $this->firstTimestamp = $data["ts"];

            ($this->callback)($data["ts"], $colname, $mu, $data["value"]);
        }
        $this->lastTimestamp = $this->reader->get_closing_timestamp();
        $this->reader->close();
        $this->reader = null;
        //$this->reader = null;
        return $index;
    }


    public function readGenerator(array $cols = null) : \Generator
    {
        $this->datasetsRead = 0;
        while($data = $this->reader->read()) {
            $colname = $data["colname"];

            if ($this->firstTimestamp === null)
                $this->firstTimestamp = $data["ts"];

            if ($cols !== null && ! in_array($colname, $cols)) {
                $this->reader->ignore_previous_column();
                continue;
            }
            $this->datasetsRead++;
            yield $data;
        }
        $this->lastTimestamp = $this->reader->get_closing_timestamp();
        $this->reader->close();
        $this->reader = null;
    }




}
