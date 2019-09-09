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

    public function open (string $filename)
    {
        $this->reader = new \UrdtsfmtReader();
        $this->reader->open($filename);

    }


    public function setOnDataCallback(callable $cb)
    {
        $this->callback = $cb;
    }




    public function read(array $cols = null) : int
    {

        $index = 0;
        while($data = $this->reader->read()) {
            [$colname, $mu] = explode(":", $data["colname"]);

            if ($cols !== null && ! in_array($colname, $cols)) {
                $this->reader->ignore_previous_column();
                continue;
            }
            $index++;


            ($this->callback)($data["ts"], $colname, $data["value"], $mu);
        }
        $this->reader->close();
        //$this->reader = null;
        return $index;
    }




}
