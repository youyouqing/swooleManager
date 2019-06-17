<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 18:11
 */
namespace app\common;
class result
{
    public $code;
    public $msg;
    public $data;

    public function __construct($code,$data,$msg)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;

    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

}