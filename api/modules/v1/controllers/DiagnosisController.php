<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/16
 * Time: 下午2:59
 */
namespace api\modules\v1\controllers;

use yii;
use api\modules\ApiBaseController;
use api\models\Appointment;
use common\models\AppointmentSearch;

use yii\helpers\ArrayHelper;
use common\service\Service;

class DiagnosisController extends ApiBaseController
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



		$queryParam = Yii::$app->request->queryParams;
		$pageSize = isset($queryParam['size']) ? $queryParam['size'] : Yii::$app->params['list.pagesize'];

		$params['AppointmentSearch']['expert_uuid'] = isset($queryParam['expert_uuid']) ? $queryParam['expert_uuid'] : null;

		if(isset($queryParam['date'])){
			$date= $queryParam['date'];
			$datetime_start=strtotime("$date 00:00:00");
			$datetime_end=strtotime("$date 23:59:59");
			$params['AppointmentSearch']['start_time']=$datetime_start;
			$params['AppointmentSearch']['end_time']=$datetime_end;
		}

		$params['AppointmentSearch']['patient_name'] = isset($queryParam['patient_name']) ? $queryParam['patient_name'] : null;

		$appiontSearch = new AppointmentSearch();
		$provider = $appiontSearch->search($params,$pageSize);
		$data = $provider->getModels();

		if($data){
			$totalPage = ceil($provider->totalCount / $pageSize);

			if(!isset($queryParam['page']) || $queryParam['page'] <= 0){
				$currentPage = 1;
			}else{
				$currentPage = $queryParam['page'] >= $totalPage ? $totalPage : $queryParam['page'];
			}
			$result=array();
			foreach($data as $item){
				$app=array();
				$app['appointment_no'] = $item->attributes['appointment_no'];
				//$app['clinic_uuid'] = $item->attributes['clinic_uuid'];

				$app['order_starttime'] = date('Y-m-d h:i',$item->attributes['order_starttime']);
				$app['patient_name'] = $item->attributes['patient_name'];
				$app['patient_description'] = $item->attributes['patient_description'];
				//$app['status'] = $item->attributes['status'];
				//专家信息
				$expert = $item->expertUu;
				$app['expert_name']= $expert->attributes['name'];

				$result[]=$app;
			}

			$result['extraFields']['currentPage'] = $currentPage;
			$result['extraFields']['totalCount'] = $provider->totalCount;
			$result['extraFields']['totalPage'] = $totalPage;
			return Service::sendSucc($result);
		}else{
			return Service::sendError(20301,'暂无数据');
		}
	}


	public function actionDetail(){

		$get_params = Yii::$app->request->get();
		if(!isset($get_params['appointment_no']) ){
		    return Service::sendError(20302,'缺少预约单号');
	    }
		$appointment_no= $get_params['appointment_no'];

		$op = $get_params['op'];
		if(!in_array($op,array('update','detail'))){
			return Service::sendError(20306,'参数错误');
		}


		$user = \yii::$app->user->identity;
		$uuid = $user->uuid;

		// $result = Appointment::findOne(['appointment_no'=>$appointment_no])->attributes;
		$appointment = Appointment::findOne(['appointment_no'=>$appointment_no,'clinic_uuid'=>$uuid]);

		if(!$appointment){
			$result['code']='20303';
			$result['message']='获取预约信息失败';
		}else{
			$result=array();
			$data= $appointment->attributes;
			$expert = $appointment->expertUu;
//			$zhumu = $appointment->appointmentVideos;

			$result['appointment_no']=$data['appointment_no'];

			$result['expert_name']=$expert['name'];
			$result['real_starttime']=date('Y-m-d h:i',$data['real_starttime']);
			$result['real_endtime']=date('Y-m-d h:i',$data['real_endtime']);
			$result['patient_description']=$data['patient_description'];
			if($data['patient_img1']){
				$result['patient_img1']=Yii::$app->params['domain'].$data['patient_img1'];
			}
			if($data['patient_img2']){
				$result['patient_img2']=Yii::$app->params['domain'].$data['patient_img2'];
			}
			if($data['patient_img3']){
				$result['patient_img3']=Yii::$app->params['domain'].$data['patient_img3'];
			}

			$result['patient_name']=$data['patient_name'];
			$result['patient_age']=$data['patient_age'];
			$result['patient_gender']=$data['patient_gender'];
			$result['patient_mobile']=$data['patient_mobile'];
			$result['patient_idcard']=$data['patient_idcard'];

			$result['expert_diagnosis']=$data['expert_diagnosis'];

			if(!empty($data['audio_url'])){
				$result['audio_url'] = \Yii::$app->urlManager->createAbsoluteUrl(['v1/zhumu/getmp3','appointment_no'=>$appointment_no]);
			}else{
				$result['audio_url'] = '';
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

		$user = \yii::$app->user->identity;
		$uuid = $user->uuid;

		$appointment =Appointment::findOne(['appointment_no'=>$appointment_no,'clinic_uuid'=>$uuid]);

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