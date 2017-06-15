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
	public $modelClass = 'api\models\Order';//对应的数据模型处理控制器

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
				],
			]
		]);
	}

	public function actions()
	{
		$actions = parent::actions();
		// 禁用""index,delete" 和 "create" 操作
//        unset($actions['index'], $actions['delete'], $actions['create'], $actions['update']);

		return $actions;

	}


	public function actionIndex()
    {
	    //$user = new User();
        $query = Order::find();
        $orders = new yii\data\ActiveDataProvider(['query' => $query]);
        $data = $orders->getModels();
        return $data;
    }

    public function actionCreate(){
	    $order_post = Yii::$app->request->post();

	    $uid=21;
	    $appointment_no= date('ymdHis').sprintf("%03d",substr($uid,-3)).rand(100,999);

	    $appointment = new Appointment();
	    $appointment->appointment_no =$appointment_no;
	    $appointment->clinic_uuid=$order_post['clinic_uuid'];
	    $appointment->expert_uuid=$order_post['expert_uuid'];
	    $appointment->order_starttime=$order_post['order_starttime'];
	    $appointment->order_endtime=$order_post['order_endtime'];
	    $appointment->patient_name=$order_post['patient_name'];
	    $appointment->patient_description=$order_post['patient_description'];
	    $appointment->fee_type=$order_post['fee_type'];
	    $appointment->create_at=time();
	    $appointment->update_at=time();

	    if($appointment->save()>0){
	    	echo 111;
	    }else{
//		    echo $model::getRawSql();
		    var_dump($appointment->getErrors());
		   // var_dump($model);
	    }
	    exit();

    }

}
