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
    /**
     * 
     * @param array|null $data
     * @return mixed
     */
    public function message(array $data);
    
    public function onClose();
    
    public function setNext(OticMiddleware $next);
}



