<?php


namespace Otic;


use Otic\OticBase;
use Otic\OticMiddleware;
use Otic\OticPack;
use Otic\OticPackChannel;

class OticWriter extends OticBase implements OticMiddleware
{
    private $packer;
    private $columns;
    private $file;
    /**
     * @var OticPackChannel
     */
    private $channel;

    public function open (string $filename)
    {
        $this->file = fopen($filename, "w");
        $this->packer = new OticPack($this->file);
        $this->channel = $this->packer->defineChannel(1, 0, 0);
        $this->columns = [];
    }

    /**
     * @inheritDoc
     */
    public function message(array $data)
    {
        $this->inject($data["ts"], $data["colname"], $data["value"], $data["metadata"]);
    }

    public function onClose()
    {
        // TODO: Implement onClose() method.
    }

    public function setNext(OticMiddleware $next)
    {
        throw new \InvalidArgumentException("OticWriter is last element of chain. You cannot call setNext() here.");
    }

    public function close() {
        $this->packer->close();
        fclose($this->file);
    }

    public function inject (float $timestamp, string $columnName, $value, string $mu)
    {
//        $value = $this->transformValue($value);
        $value = $this->transform($value);
        $this->channel->inject($timestamp, $columnName, $mu, $value);

    }

    public function getStats() {
        return $this->channel->getStats();
    }

    private function transform($value) {
        if (is_numeric($value)) {
            $intVal = (int)$value;
            $floatVal = (float)$value;
            return $intVal == $floatVal ? $intVal : $floatVal;
        } else if ($value === "") {
            return null;
        }
        return $value;
    }

    private function transformValue($value) {
        if ($value === "") {
            $value = null;
        } elseif (is_numeric($value)) {
            if (strpos($value, "e") !== false) {
                $value = (float)$value;
            } elseif (strpos($value, ".") !== false) {
                $value = (float)$value;
                if ($value < 0.0000000001 && $value > -0.0000000001)
                    $value = 0;
            } else {
                $value = (int)$value;
            }
        }
        return $value;
    }
}
