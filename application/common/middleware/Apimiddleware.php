<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2019/11/6
 * Time: 23:06
 */
namespace app\common\middleware;

use app\common\Exception\AppErrorException;
use app\common\Exception\ParamErrorException;
use app\common\Exception\SignErrorException;
use app\common\middleware\encryption\ApiService;

class Apimiddleware{


    //接口不需要参与签名的参数集合
    protected $notSignKey = [];

    public function __construct()
    {
        $this->apiService = new ApiService();
    }

    // 验证入口
    public function handle($inputs){

        // 设置参数
        $this->apiService->setBaseParam($inputs);
        //设置接口 不参与签名的参数
        $this->apiService->setNotSignKey($this->getNotSignKey());

        //数据验证
        if (config('apiMiddlewareStatus'))
        {

            try{
                return $this->apiService->check();
            } catch (AppErrorException $e)
            {
                //app_id错误
                return $e->getMessage();

            } catch (ParamErrorException $e)
            {
                //公共参数缺失
                return $e->getMessage();
            } catch (SignErrorException $e)
            {
                //签名错误
                return $e->getMessage();
            }

        }
        return true;
    }

    public function getNotSignKey(){

        $configNotSignKey = config('api.not_sign.key');
        if(!empty($configNotSignKey)){
            $this->notSignKey = $configNotSignKey;
        }
        return $this->notSignKey;
    }
}