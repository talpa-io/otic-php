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
        if ( ! extension_loaded("otic")) {
            throw new \InvalidArgumentException("libotic_php extension missing");
        }
    }

    public function __construct()
    {
        $this->loadExtensionIfNeeded();
    }

}
