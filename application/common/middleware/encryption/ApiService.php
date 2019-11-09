<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2019/11/6
 * Time: 23:11
 */

namespace app\common\middleware\encryption;
use app\common\Exception\AppErrorException;
use app\common\Exception\ParamErrorException;
use app\common\Exception\SignErrorException;
use app\common\validate\api\SignValidate;

/**中间系统Api服务
 * Class Api
 * @package App\Service\Meiquick
 * @method setMethod 设置api的method参数
 * @method post 请求api
 *
 */
class ApiService
{
    protected $app_secret;
    protected $app_id;
    protected $method;
    protected $format = 'json';
    protected $sign_method = 'md5';
    protected $lang = 'zh-CN';
    protected $sign = '';
    protected $notSignKeys = array();
    protected $params;
    protected $service;
    protected $validate;
    protected $app;

    public function __construct(
        $params = array()
    )
    {
        $this->setBaseParam($params);
        $this->validate = new SignValidate();
    }

    /**设置api基本参数
     * @param array $params 必传参数
     * [
     *  'app_id',
     *  'methd',
     *  'format',
     *  'sign_method',
     *  'sign'
     * ]
     */
    public function setBaseParam(
        $params
    )
    {
        $this->params = $params;
        $this->app_id = array_get($params, 'app_id', '');
        $this->method = array_get($params, 'method', '');
        $this->format = array_get($params, 'format', '');
        $this->sign_method = array_get($params, 'sign_method', '');
        $this->sign = array_get($params, 'sign', '');

    }

    /** 检查请求数据是否正确
     * @return bool
     * @throws AppErrorException
     * @throws ParamErrorException
     * @throws SignErrorException
     */
    public function check()
    {
        //验证参数
        $this->checkParam();
        //app_id不存在
        if (!$app = $this->getApp()) {
            throw new AppErrorException('app_id 不存在');
        }
        //秘钥签名
        $this->app_secret = $app['app_secret'];
        $preSign = $this->params;
        //验证签名
        if ($this->getSign($preSign) !== $this->sign) {
            throw new SignErrorException('签名不正确');
        }
        return true;
    }

    /** 验证参数
     * @return bool
     * @throws ParamErrorException
     */
    public function checkParam()
    {
        //验证部分数据合法性
        if (!$this->validate->check($this->params)) {
            throw new ParamErrorException($this->validate->getError());
            return false;
        }
        return true;
    }

    /**获取应用
     * @return mixed
     */
    public function getApp()
    {
        if (!$this->app)
        {
            $appConfig = config('apiMiddleware');
            if($this->app_id == $appConfig['app_id']) $this->app = $appConfig;
        }
        return $this->app;
    }

    /**获取接口方法
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }


    /** 设置不参与签名的键名
     * @param array $keyArray
     */
    public function setNotSignKey(array $keyArray)
    {
        $this->notSignKeys = array_unique(array_merge($this->notSignKeys, $keyArray));
    }

    /**过滤剩 只参与签名的数据
     * @param $data
     * @return array
     */
    private function filterSignData($data): array
    {
        $this->notSignKeys[] = 'sign';
        //过滤不需签名的数据
        foreach ($data as $key => $value) {
            if (in_array($key, $this->notSignKeys)) {
                unset($data[$key]);
            }
        }
        return $data ?? [];
    }

    /**
     * 获取签名
     * @param array $params 待签名的数据
     * [
     *  'app_secrect'
     * ]
     * @param array $notSignKeys 不参与签名的数据键名
     * @return string
     * @throws SignErrorException
     */
    public function getSign(array $params, array $notSignKeys = array()): string
    {
        $this->setNotSignKey($notSignKeys);
        $params = $this->filterSignData($params);
        $signClass = $this->getSignClassByMethod($params['sign_method']);
        $signObj = new $signClass();
        if (!$signObj instanceof SignInterface) {
            throw new SignErrorException('-007');
        }
        //签名秘钥
        $params['app_secret'] = $this->app_secret;
        return $signObj->sign($params);
    }

    /**
     * 根据秘钥生成签名
     * @param array $params
     * @param string $appId
     * @param array $notSignKeys
     * @return string
     * @throws AppErrorException
     * @throws SignErrorException
     */
    public function getSignWithAppid(array $params, string $appId, array $notSignKeys = array())
    {
        $this->app_id = $appId;
        $app = $this->getApp();
        if (!$app) {
            $errorCode = '-006';
            throw new AppErrorException($errorCode);
        }
        $this->app_secret = $app->app_secret;
        return $this->getSign($params, $notSignKeys);
    }

    /**根据签名方法获取签名类
     * @param $method
     * @return string
     * @throws SignErrorException
     */
    protected function getSignClassByMethod($method)
    {
        $signClassName = 'Sign' . ucfirst($method);
        $signClassName = "\\app\\common\\middleware\\encryption\\" . $signClassName;
        if (!class_exists($signClassName)) {
            throw new SignErrorException('-007');
        }
        return $signClassName;
    }


}