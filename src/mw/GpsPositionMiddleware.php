<?php


namespace Otic\mw;


use Otic\AbstractOticMiddleware;

class GpsPositionMiddleware extends AbstractOticMiddleware
{
    public function message(array $data)
    {
        $this->next->message($data);
        if ($data[1] !== "gps_position") {
            return;
        }
        $value = $data[3];

        $this->next->message($data);
        return;
    }
}
