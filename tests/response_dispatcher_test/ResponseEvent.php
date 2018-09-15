<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/15
 * Time: 9:21
 */

class ResponseEvent extends \Aw\Event
{
    public $request;
    public $response;

    public function __construct($response, $request)
    {
        $this->response = $response;
        $this->request = $request;
    }
}