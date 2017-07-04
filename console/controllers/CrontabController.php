<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/7/4
 * Time: 上午10:57
 */


namespace console\controllers;

use yii;
use common\models\ExpertTime;
use common\models\Expert;

use common\service\Service;
use common\models\Appointment;
use yii\console\Controller;

class CrontabController extends Controller
{
	public $time_conf = array(  '1_1'=>'周一上午','1_2'=>'周一下午','1_3'=>'周一晚上',
		'2_1'=>'周二上午','2_2'=>'周二下午','2_3'=>'周二晚上',
		'3_1'=>'周三上午','3_2'=>'周三下午','3_3'=>'周三晚上',
		'4_1'=>'周四上午','4_2'=>'周四下午','4_3'=>'周四晚上',
		'5_1'=>'周五上午','5_2'=>'周五下午','5_3'=>'周五晚上',
		'6_1'=>'周六上午','6_2'=>'周六下午','6_3'=>'周六晚上',
		'0_1'=>'周日上午','0_2'=>'周日下午','0_3'=>'周日晚上',);
	public $time_range=array(1=>'8:00-12:00',2=>'14:00-17:00',3=>'19:00-21:00');


	public function actionCreatfreetime()
	{
		echo 'start';
		$experts = Expert::find()->all();

		if($experts){
			foreach($experts as $expert){
				$exit_time=array();
				$uuid = $expert->attributes['user_uuid'];
				$new_time = $this->ordertime($uuid,json_decode($expert->attributes['free_time'],true));

				$ex_time = ExpertTime::find()->select(['date','hour','zone'])->where(['expert_uuid'=>$uuid])->andWhere(['>','date',date('Y-m-d')])->all();

				if($ex_time){
					foreach($ex_time as $v){
						$tmp['expert_uuid']= $uuid;
						$tmp['date']= $v->attributes['date'];
						$tmp['hour']= $v->attributes['hour'];
						$tmp['zone']= $v->attributes['zone'];
						$exit_time[] = $tmp;
					}
					$times =array_filter($new_time, function($v) use ($exit_time) { return ! in_array($v, $exit_time);});
				}else{
					$times=$new_time;
				}
				Yii::$app->db->createCommand()->batchInsert(ExpertTime::tableName(),['expert_uuid','date','hour','zone'],$times)->execute();

			}


		}
		echo 'finish';exit();


	}


	public function actionOrdersms()
	{
		$start_time = time()+1800;
		$end_time= $start_time+3600;

		$appointments =Appointment::find()->select('patient_name,patient_mobile')->where(['between','order_starttime',$start_time,$end_time])->all();
		if($appointments){
			foreach ($appointments as $item){
				$patient_name = $item->attributes['patient_name'];
				$patient_mobile = $item->attributes['patient_mobile'];
				$msg = "$patient_name 您好，您的会诊将于半小时后开始，请您准备";
				Service::sendSms($patient_mobile,$msg);
			}

		}

	exit();
	}


	/**
	 * @param $uuid
	 * @param $times
	 * @param string $op
	 * @return bool|int|string
	 * 处理预约表
	 */
	private function ordertime($uuid,$times){


		$expert_times=array();
		for($i=1;$i<30;$i++){
			$week = date("w",strtotime("+$i day"));
			if(key_exists($week,$times)){
				$cur_day = date('Y-m-d', strtotime("+$i day"));
				$range = $times[$week];
				foreach($range as $v){

					$tmp = explode('-',$v);
					$start = $tmp[0];
					$end = $tmp[1];

					$start_time = strtotime($cur_day.' '.$start.':00');
					$end_time = strtotime($cur_day.' '.$end.':00');

					for($start_time;$start_time<$end_time;$start_time=$start_time+1800){
						$tmp_time=array();
						$tmp_time['expert_uuid']=$uuid;
						$tmp_time['date']=$cur_day;
						$tmp_time['hour']=(int)date('H',$start_time);
						if($start_time%3600==0){
							$tmp_time['zone']=1;
						}else{
							$tmp_time['zone']=2;
						}
						$expert_times[]=$tmp_time;
					}

				}

			}

		}
		return $expert_times;

	}



}