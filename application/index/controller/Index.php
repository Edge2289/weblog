<?php
namespace app\index\controller;

use app\index\common\Base;

class Index extends Base
{

	public function __construct(){
		parent::__construct();
	}

    public function index()
    {
    	
        return $this->fetch();
    }

    public function main()
    {
        return $this->fetch('main');
    }
}
