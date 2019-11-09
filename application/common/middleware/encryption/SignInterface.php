<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2019/11/6
 * Time: 23:09
 */
namespace app\common\middleware\encryption;

interface SignInterface{

    public function sign($params);
}