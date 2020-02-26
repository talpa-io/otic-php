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

    public $datasetsRead = 0;

    public function open (string $filename)
    {
        $this->file = fopen($filename, "r");
        $this->unpacker = new OticUnpack($this->file);
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

    public function read(array $cols = null)
    {
        if(!empty($cols))
            $this->channel->setFetchList(...$cols);

        while (!feof($this->file)) {
            $this->unpacker->parse();
        }
        $this->close();
    }

    public function close() {
        $this->unpacker->close();
        fclose($this->file);
    }

    public function readGenerator(array $cols = []) {

        $data = [];
        $this->setOnDataCallback(function ($ts, $name, $unit, $value) use (&$data)  {
            $data[] = ['ts'=>$ts, 'colname'=>$name, 'metadata'=>$unit, 'value'=>$value];
        });

        if(!empty($cols))
            $this->channel->setFetchList(...$cols);

        while (!feof($this->file)) {
            $this->unpacker->parse();
            foreach ($data as $line) {
                $this->datasetsRead++;
                yield $line;
            }
            $data = [];
        }
        $this->close();
    }

    public function generate(array $cols = []) {
        $this->channel = $this->unpacker->selectChannel(1, function (){});
        if(!empty($cols))
            $this->channel->setFetchList(...$cols);
        $skips=0;
        $i=0;
        while (1)
        {
            $i++;
            $z = $this->unpacker->generate();
            if(is_array($z)) {
                if(count($z)!==5) {
                    $skips++;
                    continue;
                }
                yield $z;
            }
            $z = $this->unpacker->generate();
            $skips++;
            if ($z === null) {
                $skips++;
                $excess = $i-$skips;
                echo "break in line $i. skipped $skips. excess $excess\n";
                break;
            }
        };

        $this->close();

    }

}
