<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/16
 * Time: 下午2:59
 */
namespace api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use api\models\Appointment;
use common\models\AppointmentSearch;

use yii\helpers\ArrayHelper;
use common\service\Service;

class DiagnosisController extends ActiveController
{
		//203XX
	public $modelClass = 'api\models\Appointment';//对应的数据模型处理控制器

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'authenticator' => [
				'optional' => [
					'index',
					'view',
					'create',
					'search',
					'update',
					'delete',
					'detail',
					'checkpay',
				],
			]
		]);
	}

	public function actions()
	{
		$actions = parent::actions();
		// 禁用""index,delete" 和 "create" 操作
		unset($actions['index'], $actions['delete'], $actions['create'], $actions['update']);

		return $actions;

	}


	public function actionIndex()
	{
		$where_expert=$and_where_date=$and_where_patient=array( );

		$get_params = Yii::$app->request->get();

		if(isset($get_params['expert'])){
			$expert= $get_params['expert'];
			$where_expert['expert_uuid']=$expert;
		}

		if(isset($get_params['date'])){
			$date= $get_params['date'];
			$datetime_start=strtotime("$date 00:00:00");
			$datetime_end=strtotime("$date 23:59:59");
			$and_where_date=['between', 'order_starttime', $datetime_start, $datetime_end];
		}
		if(isset($get_params['name'])){
			$and_where_patient=['like','patient_name' ,$get_params['name']];
		}

		$data =Appointment::find()
		->where($where_expert)
		->andWhere($and_where_date)
		->andWhere($and_where_patient)
		->all();

		if($data){
			$result=$data;
		}else{
			$result['code']='20301';
			$result['message']='没有数据';
		}
		return $result;
	}


	public function actionDetail(){

		$get_params = Yii::$app->request->get();
		if(!isset($get_params['appointment_no']) ){
		    return Service::sendError(20302,'缺少预约单号');
	    }
		$appointment_no= $get_params['appointment_no'];

		// $result = Appointment::findOne(['appointment_no'=>$appointment_no])->attributes;
		$appointment = Appointment::findOne(['appointment_no'=>$appointment_no]);

		if(!$appointment){
			$result['code']='20303';
			$result['message']='获取预约信息失败';
		}else{
			$result= $appointment->attributes;
			$clinic = $appointment->clinicUu;
			$expert = $appointment->expertUu;
			$zhumu = $appointment->appointmentVideos;
			$result['clinic']=$clinic->attributes;
			$result['expert']=$expert->attributes;
			if($zhumu){
				foreach($zhumu as $v){
					$result['zhumu'][]=$v->attributes;
				}
			}
		}
		return $result;
	}


	/**
	 * 填写诊断
	 * @return mixed
	 */
	public function actionUpdate(){
		$order_post = Yii::$app->request->post();

		if(!isset($order_post['appointment_no']) ){
		    return Service::sendError(20302,'缺少预约单号');
	    }
		$appointment_no=$order_post['appointment_no'];
		$appointment =Appointment::findOne(['appointment_no'=>$appointment_no]);

		if($appointment){
			$appointment_old = $appointment->attributes;
			$now =time();
			if($appointment_old['real_endtime']+3600<$now){
				$result['code']='20304';
				$result['message']='超过修改时间';
			}else{
				//患者信息
				$appointment_new['patient_gender']=$order_post['patient_gender'];
				$appointment_new['patient_mobile']=$order_post['patient_mobile'];
				$appointment_new['patient_idcard']=$order_post['patient_idcard'];
				$appointment_new['patient_age']=$order_post['patient_age'];

				$appointment_new['expert_diagnosis']=$order_post['expert_diagnosis'];

				$appointment_new['real_endtime']=$now ;

				$op_status=Appointment::updateAll($appointment_new,['appointment_no'=>$appointment_no]);

				if($op_status>0){
					$result['appointment_no'] =$appointment_no;
				}else{
					$result['code']='20305';
					$result['message']='修改失败';
				}
			}


		}else{
			$result['code']='20303';
			$result['message']='诊断号错误';
		}
		return $result;
	}
}