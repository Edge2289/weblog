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
        // 磁盘情况
        $gauge = dailStatisticsDiskRoom();
        $g = explode('%','40%');//$gauge[4]);

        // pv uv 情况
        $startTime = strtotime(Date("Y-m-").'1 00:00:00');
        // 用户登录情况
        $user = Db::query("SELECT FROM_UNIXTIME(time,'%d') days,COUNT(log_id) count FROM blog_userlogin_log where time > ".$startTime." GROUP BY days;");  // $用户登录 数据
        $pv = Db::query("SELECT FROM_UNIXTIME(source_time,'%d') days,COUNT(source_id) count FROM blog_source where source_time > ".$startTime." GROUP BY days;"); // $pv 数据
        $uv = Db::query("SELECT FROM_UNIXTIME(time,'%d') days,COUNT(uv_id) count FROM blog_uv where time > ".$startTime." GROUP BY days;");  // $uv 数据
        $userData = [];
        $pvData = [];
        $uvData = [];
        $user1Data = [];
        $pv1Data = [];
        $uv1Data = [];
        $tData = [];
        foreach ($pv as $pvk => $pvv) {
           $pvData[(int)$pvv['days']] = $pvv['count'];
        }
        foreach ($uv as $uvk => $uvv) {
           $uvData[(int)$uvv['days']] = $uvv['count'];
        }
        foreach ($user as $userk => $userv) {
           $userData[(int)$userv['days']] = $userv['count'];
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
            if (empty($userData[$i])) {
                $user1Data[] = 0;
            }else{
                $user1Data[] = $userData[$i];
            }
        }
        $this->assign([
                'pvData' => json_encode($pv1Data),
                'uvData' => json_encode($uv1Data),
                'userData' => json_encode($user1Data),
                't' => $t,
                'gauge' => $g[0],
                'gaugeCount' => empty($gauge[1]) ? "20G" : $gauge[1],
            ]);
        return $this->fetch('main');
    }
}
