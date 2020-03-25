<?php


namespace Otic;


use Phore\Core\Exception\NotFoundException;

class OticReader extends OticBase
{
    /**
     * @var OticUnpackChannel
     */
    private $channel;
    /**
     * @var OticUnpack
     */
    private $unpacker;
    private $file;

    public $datasetsRead = 0;

    public function open ($filename)
    {
        if (is_string($filename)) {
            $this->file = fopen($filename, "r");
            if ($this->file === false)
                throw new NotFoundException("Otic input file '$filename': Cannot open for reading");
        } else {
            if ( ! is_resource($filename))
                throw new \InvalidArgumentException("Parameter 1 must be filename or file resource");
            $this->file = $filename;
        }

        $this->unpacker = new OticUnpack($this->file);
    }

    public function setOnDataCallback(callable $cb)
    {
        $this->channel = $this->unpacker->selectChannel(1, $cb);
    }

    public function getFirstTimestamp() : ?float
    {
        return $this->getTimeInterval(1)[0];
    }

    public function getLastTimestamp() : ?float
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
