<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 01.10.19
 * Time: 14:03
 */

namespace Demo;


use Otic\OticConfig;
use Otic\OticMiddleware;

class GpsRewriter extends OticMiddleware {

    public function message(array $data)
    {
        if ($data[1] !== "gps_position") {
            $this->next->message($data);
            return;
        }

        // Zerlege

        $this->next->message($data);
        $this->next->message(["Ts", "gps_longitude", "deg", lskj]);
        $this->next->message(["Ts", "gps_longitude", "deg", lskj]);
        $this->next->message(["Ts", "gps_longitude", "deg", lskj]);
        $this->next->message(["Ts", "gps_longitude", "deg", lskj]);
    }
}


OticConfig::SetWriterMiddleWare(new GpsRewriter());
