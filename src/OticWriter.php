<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 11:26
 */

namespace Otic;


class OticWriter extends OticBase
{

    private $writer;

    private $columns = [];

    public function open (string $filename)
    {
        $this->writer = new \UrdtsfmtWriter();
        $this->writer->open($filename);
        $this->writer->columns = [];
    }



    public function inject (float $timestamp, string $columnName, $value, string $mu)
    {
        $columnName = $columnName;

        if ( ! isset($this->columns[$columnName])) {
            $this->columns[$columnName] = $this->writer->define_column($columnName, $mu);
        }

        if ($value === "") {
            $value = null;
        } elseif (is_numeric($value)) {
            if (strpos($value, "e") !== false) {
                $value = (float)$value;
            } elseif (strpos($value, ".") !== false) {
                $value = (float)$value;
            } else {
                $value = (int)$value;
            }
        }

        $this->writer->write($this->columns[$columnName], $timestamp, $value);
    }


    public function close()
    {
        $this->writer->close();
    }

}
