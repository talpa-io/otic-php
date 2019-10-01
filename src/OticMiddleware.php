<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 01.10.19
 * Time: 13:41
 */

namespace Otic;


interface OticMiddleware
{
    public function message(array $data);

    public function setNext(OticMiddleware $next);
}



