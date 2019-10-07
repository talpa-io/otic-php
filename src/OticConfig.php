<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 01.10.19
 * Time: 13:58
 */

namespace Otic;


class OticConfig
{

    private static $writerMiddleware = [];

    public static function AddWriterMiddleWare (OticMiddleware $oticMiddleware)
    {
        self::$writerMiddleware[] = $oticMiddleware;
        if (count (self::$writerMiddleware) > 1) {
            self::$writerMiddleware[count (self::$writerMiddleware) - 2]->setNext($oticMiddleware);
        }
    }


    public static function GetWriterMiddleWareSource() : ?OticMiddleware
    {
        if (count (self::$writerMiddleware) === 0)
            return null;
        return self::$writerMiddleware[0];
    }

    public static function GetWriterMiddleWareDrain() : OticMiddleware
    {
        return self::$writerMiddleware[count(self::$writerMiddleware)-1];
    }

}
