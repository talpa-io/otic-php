<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 01.10.19
 * Time: 14:08
 */

namespace Otic;


abstract class AbstractOticMiddleware implements OticMiddleware
{


    /**
     * @var OticMiddleware
     */
    protected $next;


    public function setNext(OticMiddleware $next)
    {
        $this->next = $next;
    }

    public function onClose()
    {
        if ($this->next !== null)
            $this->next->onClose();
    }

}
