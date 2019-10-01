<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 01.10.19
 * Time: 15:46
 */
namespace Otic;


use Otic\mw\DefaultMiddleware;

OticConfig::AddWriterMiddleWare(new DefaultMiddleware());
