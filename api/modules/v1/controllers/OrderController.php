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
use common\models\ExpertTime;

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
//					'signup-test',
//					'index',
//					'view',
//					'create',
//					'search',
//					'update',
//					'delete',
//					'test',
//					'detail',
//					'checkpay',
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
	    $user = \yii::$app->user->identity;


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

    public function actionSearch(){
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

	    $appiontSearch = new AppointmentSearch();
	    $provider = $appiontSearch->search($params,$pageSize);
	    $data = $provider->getModels();


	    $totalPage = ceil($provider->totalCount / $pageSize);

	    if(!isset($queryParam['page']) || $queryParam['page'] <= 0){
		    $currentPage = 1;
	    }else{
		    $currentPage = $queryParam['page'] >= $totalPage ? $totalPage : $queryParam['page'];
	    }

	    $data['extraFields']['currentPage'] = $currentPage;
	    $data['extraFields']['totalCount'] = $provider->totalCount;
	    $data['extraFields']['totalPage'] = $totalPage;
	    return Service::sendSucc($data);
    }

	/**
	 * 我要预约/修改预约
	 * @return mixed
	 */
    public function actionCreate(){
	    $order_post = Yii::$app->request->post();
	    $appointment = new Appointment();
	    //获取诊所用户登录信息
	    $user = \yii::$app->user->identity;
	    $uid= $user->getId();
	    $uuid = $user->uuid;
	    //$uid=21;
	    $appointment_no= date('ymdHis').sprintf("%03d",substr($uid,-3)).rand(100,999);

	    $appointment->appointment_no =$appointment_no;
	    if(!isset($order_post['clinic_uuid']) ||!Clinic::findOne(['user_uuid'=>$order_post['clinic_uuid']])){
		    return Service::sendError(20202,'缺少诊所数据');
	    }

//	    $appointment->clinic_uuid=$order_post['clinic_uuid'];
//		if(!isset($order_post['expert_uuid']) ||!Expert::findOne(['user_uuid'=>$order_post['expert_uuid']])){
//			return Service::sendError(20203,'缺少专家数据');
//		}
		$appointment->expert_uuid=$uuid;


		if(isset($order_post['order_starttime'])&&isset($order_post['order_endtime'])){
			//检测时间是否允许
			$check_order_time = $this->checktime($order_post['expert_uuid'],$order_post['order_starttime'],$order_post['order_endtime']);
			if($check_order_time){
				return Service::sendError(20210,'专家预约时间冲突');
			}

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

		    $this->ordertime($order_post['expert_uuid'],$order_post['order_starttime'],$order_post['order_endtime'],$appointment_no,$order_post['expert_uuid']);

		    return Service::sendSucc();
	    }else{
		    return Service::sendError(20207,'添加失败');
	    }
    }

    public function actionUpdatepre(){
    	$get_params = Yii::$app->request->get();
	    if(!isset($get_params['appointment_no']) ){
		    return Service::sendError(20208,'缺少预约单号');
	    }
	    $appointment_no= $get_params['appointment_no'];

	    $user = \yii::$app->user->identity;
	    $uuid = $user->uuid;

	   // $result = Appointment::findOne(['appointment_no'=>$appointment_no])->attributes;
	    $appointment = Appointment::findOne(['appointment_no'=>$appointment_no,'clinic_uuid'=>$uuid]);

		if(!$appointment){
			$result['code']='20702';
			$result['message']='获取预约信息失败';
		}else{
			$result= $appointment->attributes;
			if($result->status != 1){
				return Service::sendError(20211,'目前状态不允许修改');
			}

			$clinic = $appointment->clinicUu;
			$expert = $appointment->expertUu;
			$result['clinic']=$clinic->attributes;
			$result['expert']=$expert->attributes;
		}
	    return Service::sendSucc($result);
    }

    public function actionUpdate(){
	    $order_post = Yii::$app->request->post();

	    $user = \yii::$app->user->identity;
	    $uuid = $user->uuid;

		if(!isset($order_post['appointment_no']) ){
		    return Service::sendError(20208,'缺少预约单号');
	    }
	    $appointment_no=$order_post['appointment_no'];

	    $appointment_new['appointment_no'] =$appointment_no;

//	    if(!isset($order_post['clinic_uuid']) ||!Clinic::findOne(['user_uuid'=>$order_post['clinic_uuid']])){
//		    return Service::sendError(20202,'缺少诊所数据');
//	    }
	    $appointment_new['clinic_uuid']=$uuid;
	    $appointment_old = Appointment::findOne(['appointment_no'=>$appointment_no,'clinic_uuid'=>$uuid])->attributes;
	    if(!$appointment_old){
			return Service::sendError(20212,'预约单不存在');
	    }

	    if(!isset($order_post['expert_uuid']) ||!Expert::findOne(['user_uuid'=>$order_post['expert_uuid']])){
			return Service::sendError(20203,'缺少专家数据');
		}

	    $appointment_new['expert_uuid']=$order_post['expert_uuid'];

	    $date_change=false;

	    if(isset($order_post['order_starttime'])&&isset($order_post['order_endtime'])){
	    	if($appointment_old['order_starttime']!==$order_post['order_starttime'] ||$appointment_old['order_endtime']!==$order_post['order_endtime']){
			    //检测时间是否允许
			    $check_order_time = $this->checktime($order_post['expert_uuid'],$order_post['order_starttime'],$order_post['order_endtime']);
			    if($check_order_time){
				    return Service::sendError(20210,'专家预约时间冲突');
			    }
			    $date_change=true;
	    	}
		}else{
			return Service::sendError(20204,'缺少预约时间');
		}
	    $appointment_new['order_starttime']=$order_post['order_starttime'];
	    $appointment_new['order_endtime']=$order_post['order_endtime'];
	    if(!isset($order_post['patient_name'])||!isset($order_post['patient_age'])||isset($order_post['patient_description'])){

		    return Service::sendError(20205,'患者信息不完整');
	    }
	    $appointment_new['patient_name']=$order_post['patient_name'];
	    $appointment_new['patient_age']=$order_post['patient_age'];
	    $appointment_new['patient_description']=$order_post['patient_description'];
	    if(!isset($order_post['fee_type'])){
		    return Service::sendError(20206,'缺少计费方式');
	    }
	    $appointment_new['fee_type']=$order_post['fee_type'];

	    $appointment_new['update_at']=time();

	    $op_status=Appointment::updateAll($appointment_new,['appointment_no'=>$appointment_no]);

	    if($op_status>0){

			if($date_change){
				$this->cancelorder($appointment_no,$order_post['clinic_uuid']);
				$this->ordertime($order_post['expert_uuid'],$order_post['order_starttime'],$order_post['order_endtime'],$appointment_no,$order_post['expert_uuid']);
			}

		    return Service::sendSucc();
	    }else{
		    return Service::sendError(20213,'修改失败失败');
	    }
    }

	/**
	 * 获取预约详情
	 * @return array
	 *
	 */

    public function actionDetail(){

	    $get_params = Yii::$app->request->post();
	    $user = \yii::$app->user->identity;
	    $uuid = $user->uuid;
	    if(!isset($get_params['appointment_no']) ){
		    return Service::sendError(20208,'缺少预约单号');
	    }
	    $appointment_no= $get_params['appointment_no'];

	   // $result = Appointment::findOne(['appointment_no'=>$appointment_no])->attributes;
	    $appointment = Appointment::findOne(['appointment_no'=>$appointment_no,'clinic_uuid'=>$uuid]);

		if(!$appointment){
			$result['code']='20209';
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
	    if(!isset($get_params['appointment_no']) ){
		    return Service::sendError(20208,'缺少预约单号');
	    }
	    $appointment_no= $get_params['appointment_no'];

	    $user = \yii::$app->user->identity;
	    $uuid = $user->uuid;

//	    if(!isset($get_params['clinic_uuid']) ||!Clinic::findOne(['user_uuid'=>$get_params['clinic_uuid']])){
//		    return Service::sendError(20202,'诊所不存在');
//	    }
//	    $clinic_uuid= $uuid;


 		$appointment = Appointment::findOne(['appointment_no'=>$appointment_no,'clinic_uuid'=>$uuid]);
 		if(!$appointment){
			$result['code']='20209';
			$result['message']='获取失败';
 		}else{
 			$result= $appointment->attributes;
 			if($result->status !=1){
				return Service::sendError(20214,'目前状态不允许取消');
 			}

 		}

	    $appointment_new['status']=3;

	    $appointment_new['update_at']=time();

	    $op_status=Appointment::updateAll($appointment_new,['appointment_no'=>$appointment_no,'clinic_uuid'=>$uuid]);


	    if($op_status>0){
	    	$this->cancelorder($appointment_no,$uuid);
		    return Service::sendSucc();
	    }else{
		    return Service::sendError(20215,'取消失败');
	    }
    }

    public function actionCheckpay(){
	    $user = \yii::$app->user->identity;
	    $uuid = $user->uuid;
	    //$clinic_uuid= $get_params['clinic_uuid'];

	    $nums=Appointment::find()->where(['clinic_uuid'=>$uuid,'pay_status'=>0])->count();

	    $result['nums']=$nums;
	    return Service::sendSucc($result);
    }

    public function actionTest(){
    	$expert_uuid = 'ebc3199a-f2a2-40a7-8167-7dc755106fce';
	    $start_time= '1497657600';
	    $end_time= '1497662940';
		$result = $this->checktime($expert_uuid,$start_time,$end_time,$expert_uuid);
		if($result){
			echo 111;
		}else{
			echo 2222;
		}
		exit();
    }

    private function ordertime($expert_uuid,$start_time,$end_time,$appoint_no,$clinic_uuid){

	    $date = date('Y-m-d',$start_time);
	    $use_set =array();

	    for($start_time;$start_time<$end_time;$start_time=$start_time+1800){
		    if($start_time%3600==0){
			    $hour = date('h',$start_time);
			    $use_set[(int)$hour][1]=1;
		    }else{
			    $hour = date('h',$start_time);
			    $use_set[(int)$hour][2]=1;
		    }
	    }
	    $cnt =0;

	    $new_oredr['is_order']=1;
	    $new_oredr['clinic_uuid']=$clinic_uuid;
	    $new_oredr['order_no']=$appoint_no;
	    foreach($use_set as $k_sets=>$value_sets ){
	    	foreach($value_sets as $k=>$v){
			    $op_status=ExpertTime::updateAll($new_oredr,
				    [   'expert_uuid'=>$expert_uuid,
					    'date'=>$date,
					    'hour'=>$k_sets,
					    'zone'=>$k]);
			    
		        if($op_status>0){
			        $cnt++;
		        }
	    	}
	    }
		return $cnt;
    }

    private function cancelorder($appoint_no,$clinic){
    	$new_oredr['status']=3;
    	$new_oredr['clinic_uuid']=$clinic;
	    $new_oredr['order_no']=$appoint_no;
    	$op_status = Appointment::updateAll($new_oredr,
    		['order_no'=>$appoint_no,
    		'clinic_uuid'=>$clinic]);
    	return $op_status;
    }

    private function checktime($expert_uuid,$start_time,$end_time,$clinic_uuid=null){

	    $date_start = date('Y-m-d',$start_time);
	    $date_end = date('Y-m-d',$end_time);

	    if($date_start!==$date_end){
		    return Service::sendError(20299,'不可以跨天预约');
	    }

		$use_set =array();

		for($start_time;$start_time<$end_time;$start_time=$start_time+1800){
			if($start_time%3600==0){
				$hour = date('h',$start_time);
				$use_set[(int)$hour][1]=1;
			}else{
				$hour = date('h',$start_time);
				$use_set[(int)$hour][2]=1;
			}
		}


		if($clinic_uuid){
			$times = ExpertTime::find()
				->where(['and',
						['expert_uuid'=>$expert_uuid,'date'=>$date_end],
						['or',
							['is_order'=>0],
							['clinic_uuid'=>$clinic_uuid]]])
				->all();
		}else{
			$times = ExpertTime::find()
				->where(['expert_uuid'=>$expert_uuid,'date'=>$date_end,'is_order'=>0])
				->all();
		}

	    $free_time=array();
	    if($times){
		    foreach($times as $v){
		    	$temp=$v->attributes;
			    $free_time[$temp['hour']][$temp['zone']]=1;
			    unset($temp);
		    }
	    }

	    $arr_diff = $this->array_diff_assoc_recursive($use_set,$free_time);
	    return $arr_diff;



    }


//多维数组的差集
	private function array_diff_assoc_recursive($array1,$array2){
		$diffarray=array();
		foreach ($array1 as $key=>$value){
			//判断数组每个元素是否是数组
			if(is_array($value)){
				//判断第二个数组是否存在key
				if(!isset($array2[$key])){
					$diffarray[$key]=$value;
					//判断第二个数组key是否是一个数组
				}elseif(!is_array($array2[$key])){
					$diffarray[$key]=$value;
				}else{
					$diff=$this->array_diff_assoc_recursive($value, $array2[$key]);
					if($diff!=false){
						$diffarray[$key]=$diff;
					}
				}
			}elseif(!array_key_exists($key, $array2) || $value!==$array2[$key]){
				$diffarray[$key]=$value;
			}
		}
		return $diffarray;
	}

}
