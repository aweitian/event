<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/15
 * Time: 9:05
 */

class Order
{
    public $dollar;

    public function __construct($dollar)
    {
        $this->dollar = $dollar;
    }
}