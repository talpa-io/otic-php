<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 11:26
 */

namespace Otic;


use Phore\Log\Logger\PhoreEchoLoggerDriver;
use Psr\Log\LogLevel;

class OticWriter extends OticBase implements OticMiddleware
{

    private $writer;

    private $columns = [];


    public function open (string $filename)
    {
        
        $this->writer = new \UrdtsfmtWriter();
        $this->writer->open($filename);
        $this->writer->columns = [];
    }


    /**
     *
     * Expects
     * ts(float) Timestamp
     * colname(string) ColumnName
     * value(mixed)
     * metadata(string)
     *
     * @param array $data
     */
    public function message(array $data)
    {
        $this->inject($data["ts"], $data["colname"], $data["value"], $data["metadata"]);
    }


    public function setNext(OticMiddleware $next)
    {
        throw new \InvalidArgumentException("OticWriter is last element of chain. You cannot call setNext() here.");
    }


    public function onClose()
    {
        // TODO: Implement onClose() method.
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
                if ($value < 0.00000000001 && $value > -0.0000000001)
                    $value = 0;
            } else {
                $value = (int)$value;
            }
        } elseif (is_bool($value)) {
            $value = (int)$value;
        }
        
        $this->writer->write($this->columns[$columnName], $timestamp, $value);
    }


    public function close()
    {
        $this->writer->close();
    }

}
