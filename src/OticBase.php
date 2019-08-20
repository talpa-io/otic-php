<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 20.08.19
 * Time: 11:27
 */

namespace Otic;


class OticBase
{


    protected function loadExtensionIfNeeded()
    {
        if ( ! extension_loaded("urdtsfmt")) {
            throw new \InvalidArgumentException("urdtsfmt extension missing");
        }
    }

    public function __construct()
    {
        $this->loadExtensionIfNeeded();
    }

}
