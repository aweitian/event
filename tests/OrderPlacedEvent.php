<?php

use Aw\Event;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 17:19
 */
class OrderPlacedEvent extends Event
{
    const NAME = 'order.placed';

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }
}