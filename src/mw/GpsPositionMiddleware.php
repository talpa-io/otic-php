<?php


namespace Otic\mw;


use Otic\AbstractOticMiddleware;

class GpsPositionMiddleware extends AbstractOticMiddleware
{
    public function message(array $data)
    {
        $this->next->message($data);
        if ($data["colname"] !== "gps_position") {
            return;
        }

        $jsonValues = json5_decode($data["value"]);

        foreach ($jsonValues as $jsonKey => $jsonValue){
            $timestamp = $data["ts"];
            $colName = $jsonKey;

            if (is_array($jsonValue)){
                $value = "[" . implode(',', $jsonValue) ."]";
            }else{
                $value = $jsonValue;
            }

            $this->next->message(["ts"=>$timestamp, "colname"=>$colName, "value"=>$value, "metadata" => "-"]);
        }
    }
}
