<?php
namespace app\index\controller;

use think\Db;
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
        dd(dailStatisticsDiskRoom());
        $startTime = strtotime(Date("Y-m-").'1 00:00:00');
        $user = Db::query("SELECT FROM_UNIXTIME(time,'%d') days,COUNT(uv_id) count FROM blog_uv where time > ".$startTime." GROUP BY days;");  // $用户登录 数据
        $pv = Db::query("SELECT FROM_UNIXTIME(source_time,'%d') days,COUNT(source_id) count FROM blog_source where source_time > ".$startTime." GROUP BY days;"); // $pv 数据
        $uv = Db::query("SELECT FROM_UNIXTIME(time,'%d') days,COUNT(uv_id) count FROM blog_uv where time > ".$startTime." GROUP BY days;");  // $uv 数据
        $pvData = [];
        $uvData = [];
        $pv1Data = [];
        $uv1Data = [];
        $tData = [];
        foreach ($pv as $pvk => $pvv) {
           $pvData[(int)$pvv['days']] = $pvv['count'];
        }
        foreach ($uv as $uvk => $uvv) {
           $uvData[(int)$uvv['days']] = $uvv['count'];
        }
        $t = (int)date('d');
        for ($i=1; $i <= $t; $i++) { 
            if (empty($pvData[$i])) {
                $pv1Data[] = 0;
            }else{
                $pv1Data[] = $pvData[$i];
            }
            if (empty($uvData[$i])) {
                $uv1Data[] = 0;
            }else{
                $uv1Data[] = $uvData[$i];
            }
        }
        $this->assign([
                'pvData' => json_encode($pv1Data),
                'uvData' => json_encode($uv1Data),
                't' => $t,
            ]);
        return $this->fetch('main');
    }
}
