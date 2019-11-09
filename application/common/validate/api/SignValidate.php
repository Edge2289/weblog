<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2019/11/7
 * Time: 21:23
 */
namespace app\common\validate\api;

use think\Validate;

class SignValidate extends Validate{


    protected $rules = [
        'nonce'         => 'required',
        'app_id'        => 'required',
        'method'        => 'required',
        'format'        => 'required',
        'sign_method'   => 'required',
        'sign'          => 'required',
    ];

    protected $msg = [
        'nonce.required'        => '-014',
        'app_id.required'       => '-002',
        'method.required'       => '-003',
        'format.required'       => '-004',
        'sign_method.required'  => '-005',
    ];
}