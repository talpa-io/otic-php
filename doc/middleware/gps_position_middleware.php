<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 01.10.19
 * Time: 15:17
 */



namespace Otic;



use Otic\mw\GpsPositionMiddleware;

OticConfig::AddWriterMiddleWare(new GpsPositionMiddleware());
