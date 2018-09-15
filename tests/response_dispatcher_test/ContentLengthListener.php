<?php

use Aw\EventSubscriberInterface;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/15
 * Time: 9:30
 */

class ContentLengthListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array('response' => array('onResponse', -255));
    }
}