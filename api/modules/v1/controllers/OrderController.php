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

use yii\helpers\ArrayHelper;
use common\service\Service;
#use api\models\Signup;

class OrderController extends ActiveController
{
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
//        $query = Appointment::find();
//	    $Appointment = new yii\data\ActiveDataProvider(['query' => $query]);
//        $data = $Appointment->getModels();
		$where=array(1=>1);
		$where=array( );
		$model= new Appointment();

		$data = Appointment::findAll($where);


        return $data;
    }

	/**
	 * 我要预约/修改预约
	 * @return mixed
	 */
    public function actionCreate(){
	    $order_post = Yii::$app->request->post();
	    $appointment = new Appointment();

	    $uid=21;
	    $appointment_no= date('ymdHis').sprintf("%03d",substr($uid,-3)).rand(100,999);

	    $appointment->appointment_no =$appointment_no;
	    $appointment->clinic_uuid=$order_post['clinic_uuid'];
	    $appointment->expert_uuid=$order_post['expert_uuid'];
	    $appointment->order_starttime=$order_post['order_starttime'];
	    $appointment->order_endtime=$order_post['order_endtime'];
	    $appointment->patient_name=$order_post['patient_name'];
	    $appointment->patient_age=$order_post['patient_age'];
	    $appointment->patient_description=$order_post['patient_description'];
	    $appointment->fee_type=$order_post['fee_type'];
	    $appointment->create_at=time();
	    $appointment->update_at=time();


	    if($appointment->save()>0){
	    	$result['appointment_no'] =$appointment_no;
	    }else{
	    	$result['code']='20701';
	    	$result['message']='添加失败';
	    }

	    return $result;
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
		    $result['appointment_no'] =$appointment_no;
	    }else{
		    $result['code']='20703';
		    $result['message']='修改失败';
	    }

	    return $result;
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
		return $result;
    }


    public function actionCancel(){
	    $get_params = Yii::$app->request->get();
	    $appointment_no= $get_params['appointment_no'];
	    $clinic_uuid= $get_params['clinic_uuid'];

	    $appointment_new['status']=0;

	    $appointment_new['update_at']=time();

	    $op_status=Appointment::updateAll($appointment_new,['appointment_no'=>$appointment_no,'clinic_uuid'=>$clinic_uuid]);


	    if($op_status>0){
		    $result['appointment_no'] =$appointment_no;
	    }else{
		    $result['code']='20703';
		    $result['message']='修改失败';
	    }

	    return $result;
    }

    public function actionCheckpay(){
	    $get_params = Yii::$app->request->get();
	    $clinic_uuid= $get_params['clinic_uuid'];

	    $nums=Appointment::find()->where(['clinic_uuid'=>$clinic_uuid,'pay_status'=>0])->count();

	    $result['nums']=$nums;
	    return $result;
    }

    public function checkApp(){

    }
}
