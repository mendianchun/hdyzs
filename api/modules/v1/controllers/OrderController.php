<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/14
 * Time: 下午2:45
 */
namespace api\modules\v1\controllers;

use yii;
use yii\rest\ActiveController;
use api\models\Appointment;
use common\models\AppointmentSearch;
use common\models\Clinic;
use common\models\Expert;

use yii\helpers\ArrayHelper;
use common\service\Service;
#use api\models\Signup;

class OrderController extends ActiveController
{

	//202XX
	public $modelClass = 'api\models\Appointment';//对应的数据模型处理控制器

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'authenticator' => [
				'optional' => [
					'signup-test',
					'index',
					'view',
					'create',
					'search',
					'update',
					'delete',
					'test',
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
		$where=$and_where=array( );

		$get_params = Yii::$app->request->get();

		if(isset($get_params['expert'])){
			$expert= $get_params['expert'];
			$where['expert_uuid']=$expert;
		}

		if(isset($get_params['date'])){
			$date= $get_params['date'];
			$datetime_start=strtotime("$date 00:00:00");
			$datetime_end=strtotime("$date 23:59:59");
			$and_where=['between', 'order_starttime', $datetime_start, $datetime_end];
		}

		$data =Appointment::find()->where($where)->andWhere($and_where)->all();
		if($data){
			return Service::sendSucc($data);
		}else{
			return Service::sendError(20201,'暂无数据');
		}
    }

	/**
	 * 我要预约/修改预约
	 * @return mixed
	 */
    public function actionCreate(){
	    $order_post = Yii::$app->request->post();
	    $appointment = new Appointment();
//	    $user = \yii::$app->user->identity;
//	    $uid= $user->getId();
	    $uid=21;
	    $appointment_no= date('ymdHis').sprintf("%03d",substr($uid,-3)).rand(100,999);

	    $appointment->appointment_no =$appointment_no;
	    if(!isset($order_post['clinic_uuid']) ||!Clinic::findOne(['user_uuid'=>$order_post['clinic_uuid']])){
		    return Service::sendError(20202,'缺少诊所数据');
	    }
	    $appointment->clinic_uuid=$order_post['clinic_uuid'];
		if(!isset($order_post['expert_uuid']) ||!Expert::findOne(['user_uuid'=>$order_post['expert_uuid']])){

		}else{
			return Service::sendError(20203,'缺少专家数据');
		}
		$appointment->expert_uuid=$order_post['expert_uuid'];

		if(isset($order_post['order_starttime'])&&isset($order_post['order_endtime'])){
			//检测时间是否允许

		}else{
			return Service::sendError(20204,'缺少预约时间');
		}
	    $appointment->order_starttime=$order_post['order_starttime'];
	    $appointment->order_endtime=$order_post['order_endtime'];
	    if(!isset($order_post['patient_name'])||!isset($order_post['patient_age'])||isset($order_post['patient_description'])){

		    return Service::sendError(20205,'患者信息不完整');
	    }

	    $appointment->patient_name=$order_post['patient_name'];
	    $appointment->patient_age=$order_post['patient_age'];
	    $appointment->patient_description=$order_post['patient_description'];

	    if(!isset($order_post['fee_type'])){
		    return Service::sendError(20206,'缺少计费方式');
	    }

	    $appointment->fee_type=$order_post['fee_type'];
	    $appointment->create_at=time();
	    $appointment->update_at=time();


	    if($appointment->save()>0){
		    return Service::sendSucc();
	    }else{
		    return Service::sendError(20207,'添加失败');
	    }
    }

    public function actionUpdate(){
	    $order_post = Yii::$app->request->post();

	    $appointment_no=$order_post['appointment_no'];

	    $appointment_new['appointment_no'] =$appointment_no;
	    $appointment_new['clinic_uuid']=$order_post['clinic_uuid'];
	    $appointment_new['expert_uuid']=$order_post['expert_uuid'];
	    $appointment_new['order_starttime']=$order_post['order_starttime'];
	    $appointment_new['order_endtime']=$order_post['order_endtime'];
	    $appointment_new['patient_name']=$order_post['patient_name'];
	    $appointment_new['patient_age']=$order_post['patient_age'];
	    $appointment_new['patient_description']=$order_post['patient_description'];
	    $appointment_new['fee_type']=$order_post['fee_type'];

	    $appointment_new['update_at']=time();

	    $op_status=Appointment::updateAll($appointment_new,['appointment_no'=>$appointment_no]);


	    if($op_status>0){
		    return Service::sendSucc();
	    }else{
		    return Service::sendError(20207,'添加失败');
	    }
    }

	/**
	 * 获取预约详情
	 * @return array
	 *
	 */

    public function actionDetail(){

	    $get_params = Yii::$app->request->get();
	    $appointment_no= $get_params['appointment_no'];

	   // $result = Appointment::findOne(['appointment_no'=>$appointment_no])->attributes;
	    $appointment = Appointment::findOne(['appointment_no'=>$appointment_no]);

		if(!$appointment){
			$result['code']='20702';
			$result['message']='获取失败';
		}else{
			$result= $appointment->attributes;
			$clinic = $appointment->clinicUu;
			$expert = $appointment->expertUu;
			$result['clinic']=$clinic->attributes;
			$result['expert']=$expert->attributes;
		}
	    return Service::sendSucc($result);
    }


    public function actionCancel(){
	    $get_params = Yii::$app->request->get();
	    $appointment_no= $get_params['appointment_no'];
	    $clinic_uuid= $get_params['clinic_uuid'];

	    $appointment_new['status']=0;

	    $appointment_new['update_at']=time();

	    $op_status=Appointment::updateAll($appointment_new,['appointment_no'=>$appointment_no,'clinic_uuid'=>$clinic_uuid]);


	    if($op_status>0){
		    return Service::sendSucc();
	    }else{
		    return Service::sendError(20207,'添加失败');
	    }
    }

    public function actionCheckpay(){
	    $get_params = Yii::$app->request->get();
	    $clinic_uuid= $get_params['clinic_uuid'];

	    $nums=Appointment::find()->where(['clinic_uuid'=>$clinic_uuid,'pay_status'=>0])->count();

	    $result['nums']=$nums;
	    return Service::sendSucc($result);
    }

    public function actionTest(){
		$result = $this->checktime('asda','1497657600','1497663000');
    }

    private function checktime($expert_uuid,$start_time,$end_time){
	    $date_start = date('Y-m-d',$start_time);
	    $hour_start = date('H',$start_time);
	    $minute_start = date('i',$start_time);

	    $date_end = date('Y-m-d',$end_time);
	    $hour_end = date('H',$end_time);
	    $minute_end = date('i',$end_time);
	    if($date_start!==$date_end){
		    return Service::sendError(20299,'不可以跨天预约');
	    }


		var_dump($minute_end);
		exit();

    }
}
