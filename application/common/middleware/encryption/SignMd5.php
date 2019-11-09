<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2019/11/6
 * Time: 23:09
 */
namespace app\common\middleware\encryption;

class SignMd5 implements SignInterface {
    /** 签名
     * @param $data
     * [
     *  'app_secret' => '' 必须传递该键名,不传则抛出SignErrorException异常
     * ]
     * @return string
     * @throws SignErrorException
     */
    public function sign($params)
    {
        $app_secret = $params['app_secret'] ?? null;
        if ($app_secret === null)
        {
            throw new SignErrorException('-003');
        }
        unset($params['app_secret']);

        //排序
        ksort($params);
        $tmps = array();
        foreach ($params as $k => $v) {
            if(!is_array($v)){
                $tmps[] = $k . $v;
            }
        }
        $string = strtoupper(rawurlencode(implode('', $tmps))) ;
        //组合
        $string = $app_secret . $string. $app_secret;
        //加密
        return md5($string);
    }
}