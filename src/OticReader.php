<?php


namespace Otic;


class OticReader extends OticBase
{
    private $file;
    /**
     * @var OticUnpack
     */
    private $unpacker;
    /**
     * @var OticUnpackChannel
     */
    private $channel;

    public function open (string $filename)
    {
        echo "\nopen new otic reader\n";
        $this->file = fopen($filename, "r");
        $this->unpacker = new OticUnpack($this->file);
        $this->isParsed = false;
    }

    public function setOnDataCallback(callable $cb)
    {
        $this->channel = $this->unpacker->selectChannel(1, $cb);
    }

    public function getFirstTimestamp() : ?int
    {
        return $this->getTimeInterval(1)[0];
    }

    public function getLastTimestamp() : ?int
    {
        return $this->getTimeInterval(1)[1];
    }

    public function getTimeInterval(int $channelId) : array {
        return $this->channel->getTimeInterval();
    }

    public function read(array $cols = null) : int
    {
        if($cols !== null)
            $this->channel->setFetchList(...$cols);

        while (!feof($this->file)) {
            $this->unpacker->parse();
        }
        $this->close();

        return 123;
    }

    public function close() {
        echo "\nclose new otic reader\n";
        $this->unpacker->close();
        fclose($this->file);
    }

    public function readGenerator(array $cols = null) {

        $data = [];
        $this->setOnDataCallback(function ($ts, $name, $unit, $value) use (&$data)  {
            $d['ts']=$ts;
            $d['colname']=$name;
            $d['metadata']=$unit;
            $d['value']=$value;
            $data[] = $d;
        });

        if($cols !== null)
            $this->channel->setFetchList(...$cols);

        while (!feof($this->file)) {
            $this->unpacker->parse();
            foreach ($data as $line) {
                yield $line;
            }
            $data = [];
        }
        $this->close();
    }

}
