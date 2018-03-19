<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/14
 * Time: 下午2:45
 */
namespace api\modules\v1\controllers;

use api\models\User;
use yii;
use api\modules\ApiBaseController;
use api\models\Appointment;
use common\models\AppointmentSearch;
use common\models\Clinic;
use common\models\Expert;
use common\models\ExpertTime;
use common\models\ExpertTimeSearch;
use yii\helpers\ArrayHelper;
use common\service\Service;

#use api\models\Signup;

class OrderController extends ApiBaseController
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

    /*
     * 诊所端获取预约列表
     */
    public function actionIndex()
    {
        $queryParam = Yii::$app->request->queryParams;
        $pageSize = isset($queryParam['size']) ? $queryParam['size'] : Yii::$app->params['list.pagesize'];

        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;
        $params['AppointmentSearch']['expert_uuid'] = isset($queryParam['expert_uuid']) ? $queryParam['expert_uuid'] : null;
	    $params['AppointmentSearch']['status'] = isset($queryParam['status']) ? $queryParam['status'] : null;
	    $params['AppointmentSearch']['dx_status'] = isset($queryParam['dx_status']) ? $queryParam['dx_status'] : null;
        $params['AppointmentSearch']['clinic_uuid'] = $uuid;
        if (isset($queryParam['date'])) {
            $date = $queryParam['date'];
            $datetime_start = strtotime("$date 00:00:00");
            $datetime_end = strtotime("$date 23:59:59");
            $params['AppointmentSearch']['order_starttime'] = $datetime_start;
            $params['AppointmentSearch']['order_endtime'] = $datetime_end;
        }

        $appiontSearch = new AppointmentSearch();
        $provider = $appiontSearch->search($params, $pageSize);
        $data = $provider->getModels();

        if ($data) {
            $totalPage = ceil($provider->totalCount / $pageSize);

            if (!isset($queryParam['page']) || $queryParam['page'] <= 0) {
                $currentPage = 1;
            } else {
                $currentPage = $queryParam['page'] >= $totalPage ? $totalPage : $queryParam['page'];
            }
            $result = array();
            foreach ($data as $item) {
                $app = array();
                $app['appointment_no'] = "".$item->attributes['appointment_no']."";
                $app['clinic_uuid'] = $item->attributes['clinic_uuid'];

                $app['order_starttime'] = date('Y-m-d H:i:s', $item->attributes['order_starttime']);
                $app['patient_name'] = $item->attributes['patient_name'];
	            $app['status'] = $item->attributes['status'];
	            $app['dx_status'] = $item->attributes['dx_status'];
                //专家信息
                $expert = $item->expertUu;
                $app['expert']['id'] = $expert->attributes['id'];
                $app['expert']['name'] = $expert->attributes['name'];
                $app['expert']['head_img'] = Yii::$app->params['domain'] . $expert->attributes['head_img'];
                $app['expert']['uuid'] = $item->attributes['expert_uuid'];

                $result[] = $app;
            }

            $result['extraFields']['currentPage'] = $currentPage;
            $result['extraFields']['totalCount'] = $provider->totalCount;
            $result['extraFields']['totalPage'] = $totalPage;
            return Service::sendSucc($result);
        } else {
            return Service::sendError(20201, '暂无数据');
        }

    }

    /*
     * 专家端获取预约列表
     */
    public function actionIndex2expert()
    {
        $queryParam = Yii::$app->request->queryParams;
        $pageSize = isset($queryParam['size']) ? $queryParam['size'] : Yii::$app->params['list.pagesize'];

        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;
        $params['AppointmentSearch']['clinicName'] = isset($queryParam['clinicName']) ? $queryParam['clinicName'] : null;
        $params['AppointmentSearch']['status'] = isset($queryParam['status']) ? $queryParam['status'] : null;
        $params['AppointmentSearch']['expert_uuid'] = $uuid;
        if (isset($queryParam['date'])) {
            $date = $queryParam['date'];
            $datetime_start = strtotime("$date 00:00:00");
            $datetime_end = strtotime("$date 23:59:59");
            $params['AppointmentSearch']['order_starttime'] = $datetime_start;
            $params['AppointmentSearch']['order_endtime'] = $datetime_end;
        }

        $appiontSearch = new AppointmentSearch();
        $provider = $appiontSearch->search($params, $pageSize);
        $data = $provider->getModels();

        if ($data) {
            $totalPage = ceil($provider->totalCount / $pageSize);

            if (!isset($queryParam['page']) || $queryParam['page'] <= 0) {
                $currentPage = 1;
            } else {
                $currentPage = $queryParam['page'] >= $totalPage ? $totalPage : $queryParam['page'];
            }
            $result = array();
            foreach ($data as $item) {
                $app = array();
                $app['appointment_no'] = $item->attributes['appointment_no'];
                $app['expert_uuid'] = $item->attributes['expert_uuid'];

                $app['order_starttime'] = date('Y-m-d H:i:s', $item->attributes['order_starttime']);
                $app['patient_name'] = $item->attributes['patient_name'];
                $app['status'] = $item->attributes['status'];
                $app['dx_status'] = $item->attributes['dx_status'];
                //诊所信息
                $clinic = $item->clinicUu;
                $app['clinic']['id'] = $clinic->attributes['id'];
                $app['clinic']['name'] = $clinic->attributes['name'];
                $app['clinic']['uuid'] = $clinic->attributes['user_uuid'];

                $result[] = $app;
            }

            $result['extraFields']['currentPage'] = $currentPage;
            $result['extraFields']['totalCount'] = $provider->totalCount;
            $result['extraFields']['totalPage'] = $totalPage;
            return Service::sendSucc($result);
        } else {
            return Service::sendError(20201, '暂无数据');
        }

    }

    public function actionSearch()
    {
        $queryParam = Yii::$app->request->queryParams;
        $pageSize = isset($queryParam['size']) ? $queryParam['size'] : Yii::$app->params['list.pagesize'];
        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;
        $params['AppointmentSearch']['expert_uuid'] = isset($queryParam['expert_uuid']) ? $queryParam['expert_uuid'] : null;
        $params['AppointmentSearch']['clinic_uuid'] = $uuid;
        if (isset($queryParam['date'])) {
            $date = $queryParam['date'];
            $datetime_start = strtotime("$date 00:00:00");
            $datetime_end = strtotime("$date 23:59:59");
            $params['AppointmentSearch']['order_starttime'] = $datetime_start;
            $params['AppointmentSearch']['order_endtime'] = $datetime_end;
        }

        $appiontSearch = new AppointmentSearch();
        $provider = $appiontSearch->search($params, $pageSize);
        $data = $provider->getModels();


        $totalPage = ceil($provider->totalCount / $pageSize);

        if (!isset($queryParam['page']) || $queryParam['page'] <= 0) {
            $currentPage = 1;
        } else {
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
    public function actionCreate()
    {
        $order_post = Yii::$app->request->post();
        $appointment = new Appointment();
        //获取诊所用户登录信息
        $user = \yii::$app->user->identity;
        $uid = $user->getId();
        $uuid = $user->uuid;
        //$uid=21;
        $appointment_no = date('ymdHis') . sprintf("%03d", substr($uid, -3)) . rand(100, 999);

        $appointment->appointment_no = $appointment_no;
//	    if(!isset($order_post['clinic_uuid']) ||!Clinic::findOne(['user_uuid'=>$order_post['clinic_uuid']])){
//		    return Service::sendError(20202,'缺少诊所数据');
//	    }

        $appointment->clinic_uuid = $uuid;


        if (!isset($order_post['expert_uuid']) || !Expert::findOne(['user_uuid' => $order_post['expert_uuid']])) {
            return Service::sendError(20203, '缺少专家数据');
        }
        $appointment->expert_uuid = $order_post['expert_uuid'];

        if (isset($order_post['order_starttime']) && isset($order_post['order_endtime'])) {

            $start_time = strtotime($order_post['order_starttime']);
            $end_time = strtotime($order_post['order_endtime']);
            if (($start_time - time()) > 3600 * 24 * 7) {
                return Service::sendError(20217, '只能预约一周内');
            }

            //检测时间是否允许
            $check_order_time = $this->checktime($order_post['expert_uuid'], $start_time, $end_time);
            if ($check_order_time) {
                return Service::sendError(20210, '专家预约时间冲突');
            }

        } else {
            return Service::sendError(20204, '缺少预约时间');
        }
        $appointment->order_starttime = $start_time;
        $appointment->order_endtime = $end_time;
        if (!isset($order_post['patient_name']) || !isset($order_post['patient_description'])) {

            return Service::sendError(20205, '患者信息不完整');
        }

        $appointment->patient_name = $order_post['patient_name'];
        $appointment->patient_description = $order_post['patient_description'];

        $appointment->patient_img1 = isset($order_post['patient_img1']) ? $order_post['patient_img1'] : '';
        $appointment->patient_img2 = isset($order_post['patient_img2']) ? $order_post['patient_img2'] : '';
        $appointment->patient_img3 = isset($order_post['patient_img3']) ? $order_post['patient_img3'] : '';


        if (!isset($order_post['pay_type'])) {
            return Service::sendError(20206, '缺少计费方式');
        }


        $appointment->pay_type = $order_post['pay_type'];

        $expert = new Expert();
        $cost = $expert->getExpertCost($order_post['expert_uuid']);

        $appointment->order_fee = $cost['fee'];
        $appointment->order_score = $cost['score'];

        $appointment->created_at = time();
        $appointment->updated_at = time();

        if ($appointment->save() > 0) {
            //积分操作
            if ($order_post['pay_type'] == Appointment::PAY_TYPE_SCORE) {
                $cli = new Clinic();
                $cli->updateScore(-$cost['score'], " add order: $appointment_no");
            }

            //操作预约表
            //  $this->ordertime($order_post['expert_uuid'],$order_post['order_starttime'],$order_post['order_endtime'],$appointment_no,$order_post['expert_uuid']);

            return Service::sendSucc();
        } else {
            return Service::sendError(20207, '添加失败');
        }
    }

    /*
     * 诊所端获取详情
     */
    public function actionDetail()
    {
        $get_params = Yii::$app->request->get();
        if (!isset($get_params['appointment_no'])) {
            return Service::sendError(20208, '缺少预约单号');
        }
        $op = $get_params['op'];
        if (!in_array($op, array('update', 'detail'))) {
            return Service::sendError(20216, '参数错误');
        }
        $appointment_no = $get_params['appointment_no'];

        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;

        // $result = Appointment::findOne(['appointment_no'=>$appointment_no])->attributes;
        $appointment = Appointment::findOne(['appointment_no' => $appointment_no, 'clinic_uuid' => $uuid]);

        if (!$appointment) {
            $result['code'] = '20202';
            $result['message'] = '获取预约信息失败';
        } else {
            $result = $appointment->attributes;
            if ($result['status'] != 1 && $op == 'update') {
                return Service::sendError(20211, '目前状态不允许修改');
            }
            $result['order_starttime'] = date('Y-m-d H:i:s', $result['order_starttime']);
            $result['order_endtime'] = date('Y-m-d H:i:s', $result['order_endtime']);

            if ($result['patient_img1']) {
                $result['patient_img1'] = Yii::$app->params['domain'] . $result['patient_img1'];
	            $result['patient_img1_thumb'] = $this->thumb($result['patient_img1']);
            }
            if ($result['patient_img2']) {
                $result['patient_img2'] = Yii::$app->params['domain'] . $result['patient_img2'];
	            $result['patient_img2_thumb'] = $this->thumb($result['patient_img2']);
            }
            if ($result['patient_img3']) {
                $result['patient_img3'] = Yii::$app->params['domain'] . $result['patient_img3'];
	            $result['patient_img3_thumb'] = $this->thumb($result['patient_img3']);
            }


            unset($result['real_starttime']);
            unset($result['real_endtime']);
            unset($result['real_fee']);
            unset($result['expert_diagnosis']);
            unset($result['pay_status']);
            unset($result['created_at']);
            unset($result['updated_at']);
            unset($result['audio_url']);
            unset($result['audio_created_at']);

            $expert = $appointment->expertUu;

            $result['expert']['id'] = $expert->attributes['id'];
            $result['expert']['name'] = $expert->attributes['name'];
            $result['expert']['head_img'] = Yii::$app->params['domain'] . $expert->attributes['head_img'];

	        $result['expert']['head_img_thumb'] = $this->thumb($result['expert']['head_img']);


            $result['expert']['user_uuid'] = $expert->attributes['user_uuid'];
        }
        return Service::sendSucc($result);
    }
	private function thumb($img){
		$thumbnailFileExt = strrchr( $img, '.');
		$len = strlen($img)-strlen($thumbnailFileExt);
		$thumbnailFileName =  substr($img,0,$len) .'_200_200';
		$thumbnailFile =  $thumbnailFileName . $thumbnailFileExt;
		return $thumbnailFile;
	}
    /*
     * 专家端获取详情
     */
    public function actionDetail2expert()
    {
        $get_params = Yii::$app->request->get();
        if (!isset($get_params['appointment_no'])) {
            return Service::sendError(20208, '缺少预约单号');
        }

        $appointment_no = $get_params['appointment_no'];

        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;

        $appointment = Appointment::findOne(['appointment_no' => $appointment_no, 'expert_uuid' => $uuid]);

        if (!$appointment) {
            $result['code'] = '20202';
            $result['message'] = '获取预约信息失败';
        } else {
            $result = $appointment->attributes;
            $result['order_starttime'] = date('Y-m-d H:i:s', $result['order_starttime']);
            $result['order_endtime'] = date('Y-m-d H:i:s', $result['order_endtime']);

            if ($result['patient_img1']) {
                $result['patient_img1'] = Yii::$app->params['domain'] . $result['patient_img1'];
	            $result['patient_img1_thumb'] = $this->thumb($result['patient_img1']);
            }
            if ($result['patient_img2']) {
                $result['patient_img2'] = Yii::$app->params['domain'] . $result['patient_img2'];
	            $result['patient_img2_thumb'] = $this->thumb($result['patient_img2']);
            }
            if ($result['patient_img3']) {
                $result['patient_img3'] = Yii::$app->params['domain'] . $result['patient_img3'];
	            $result['patient_img3_thumb'] = $this->thumb($result['patient_img3']);
            }


            unset($result['real_starttime']);
            unset($result['real_endtime']);
            unset($result['real_fee']);
            unset($result['expert_diagnosis']);
            unset($result['pay_status']);
            unset($result['created_at']);
            unset($result['updated_at']);
            unset($result['audio_url']);
            unset($result['audio_created_at']);

            $clinic = $appointment->clinicUu;

            $result['clinic']['id'] = $clinic->attributes['id'];
            $result['clinic']['name'] = $clinic->attributes['name'];
            $result['clinic']['user_uuid'] = $clinic->attributes['user_uuid'];
        }
        return Service::sendSucc($result);
    }

    public function actionUpdate()
    {
        $order_post = Yii::$app->request->post();

        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;
        if (!isset($order_post['appointment_no'])) {
            return Service::sendError(20208, '缺少预约单号');
        }
        $appointment_no = $order_post['appointment_no'];

        $appointment_new['appointment_no'] = $appointment_no;

        $appointment_new['clinic_uuid'] = $uuid;
        $appointment_old = Appointment::findOne(['appointment_no' => $appointment_no, 'clinic_uuid' => $uuid]);
        if ($appointment_old) {
            $appointment_old = $appointment_old->attributes;
        } else {
            return Service::sendError(20212, '预约单不存在');
        }


        if (!isset($order_post['expert_uuid']) || !Expert::findOne(['user_uuid' => $order_post['expert_uuid']])) {
            return Service::sendError(20203, '缺少专家数据');
        }

        $appointment_new['expert_uuid'] = $order_post['expert_uuid'];

        $date_change = false;

        if (isset($order_post['order_starttime']) && isset($order_post['order_endtime'])) {

            $start_time = strtotime($order_post['order_starttime']);
            $end_time = strtotime($order_post['order_endtime']);
            if (($start_time - time()) > 3600 * 24 * 7) {
                return Service::sendError(20217, '只能预约一周内');
            }
            if ($appointment_old['order_starttime'] !== $start_time || $appointment_old['order_endtime'] !== $end_time) {
                //检测时间是否允许
                $check_order_time = $this->checktime($order_post['expert_uuid'], $start_time, $end_time);

                if ($check_order_time) {
                    return Service::sendError(20210, '专家预约时间冲突');
                }
                $date_change = true;
            }
	        $appointment_new['order_starttime'] = $start_time;
	        $appointment_new['order_endtime'] = $end_time;

        } else {
            return Service::sendError(20204, '缺少预约时间');
        }

        if (!isset($order_post['patient_name']) || !isset($order_post['patient_description'])) {

            return Service::sendError(20205, '患者信息不完整');
        }
        $appointment_new['patient_name'] = $order_post['patient_name'];
        // $appointment_new['patient_age']=$order_post['patient_age'];
        $appointment_new['patient_description'] = $order_post['patient_description'];


        $appointment_new['patient_img1'] = isset($order_post['patient_img1']) ? $order_post['patient_img1'] : '';
        $appointment_new['patient_img2'] = isset($order_post['patient_img2']) ? $order_post['patient_img2'] : '';
        $appointment_new['patient_img3'] = isset($order_post['patient_img3']) ? $order_post['patient_img3'] : '';


        if (!isset($order_post['pay_type'])) {
            return Service::sendError(20206, '缺少计费方式');
        }

        if ($appointment_old['pay_type'] != $order_post['pay_type']) {
            $appointment_new['pay_type'] = $order_post['pay_type'];
        }


        $appointment_new['updated_at'] = time();


        $expert = new Expert();
        $cost = $expert->getExpertCost($order_post['expert_uuid']);

        $op_status = Appointment::updateAll($appointment_new, ['appointment_no' => $appointment_no]);

        if ($op_status > 0) {

            if ($order_post['pay_type'] == Appointment::PAY_TYPE_SCORE && $cost['score'] != $appointment_old['order_score']) {
                $cli = new Clinic();
                $cli->updateScore($appointment_old['order_score'], " update order: $appointment_no");
                $cli->updateScore(-$cost['score'], " update order: $appointment_no");
            }

//			if($date_change){
//				$this->cancelorder($appointment_no,$order_post['clinic_uuid']);
//			//	$this->ordertime($order_post['expert_uuid'],$order_post['order_starttime'],$order_post['order_endtime'],$appointment_no,$order_post['expert_uuid']);
//			}

            return Service::sendSucc();
        } else {
            return Service::sendError(20213, '修改失败失败');
        }
    }

    /**
     *
     * 取消订单
     * @return array
     */

    public function actionCancel()
    {
        $get_params = Yii::$app->request->post();
        if (!isset($get_params['appointment_no'])) {
            return Service::sendError(20208, '缺少预约单号');
        }
        $appointment_no = $get_params['appointment_no'];

        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;


        $appointment = Appointment::findOne(['appointment_no' => $appointment_no, 'clinic_uuid' => $uuid]);
        if (!$appointment) {
            $result['code'] = '20209';
            $result['message'] = '获取失败';
        } else {
            $result = $appointment->attributes;
            if ($result['status'] != 1) {
                return Service::sendError(20214, '目前状态不允许取消');
            }

        }

        $appointment_new['status'] = 3;

        $appointment_new['updated_at'] = time();


        $op_status = Appointment::updateAll($appointment_new, ['appointment_no' => $appointment_no, 'clinic_uuid' => $uuid]);

        if ($op_status > 0) {

            if ($result['pay_type'] == Appointment::PAY_TYPE_SCORE) {
                //积分操作
                $cli = new Clinic();
                $expert = new Expert();
                $fee = $expert->getExpertCost($appointment->attributes['expert_uuid']);
                $cli->updateScore($fee['score'], "cancel order: $appointment_no");
            }

            //$this->cancelorder($appointment_no,$uuid);
            return Service::sendSucc();
        } else {
            return Service::sendError(20215, '取消失败');
        }
    }

    public function actionCheckpay()
    {
        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;
        //$clinic_uuid= $get_params['clinic_uuid'];

        $nums = Appointment::find()->where(['clinic_uuid' => $uuid, 'pay_status' => Appointment::PAY_STATUS_UNPAY, 'status' => Appointment::STATUS_SUCC])->count();

        $result['nums'] = (int)$nums;
        return Service::sendSucc($result);
    }



    public function actionSms(){
	    $params = Yii::$app->request->post();
	    if(!isset($params['op'])){
		    return Service::sendError(20218, '缺少参数');
	    }

	    if (!isset($params['appointment_no'])) {
		    return Service::sendError(20208, '缺少预约单号');
	    }
	    $appointment_no = $params['appointment_no'];
	    $op = $params['op'];
	    $user = \yii::$app->user->identity;
	    $uuid = $user->uuid;


	    if($op=='check'){
			$date = strtotime(date('Y-m-d 00:00:00',time()));


		    $appointment = Appointment::find()->where(['and',['expert_uuid'=>$uuid,'status'=>Appointment::STATUS_SUCC,'dx_status'=>Appointment::DX_STATUS_UN],['>','order_starttime',$date]])->orderBy(['appointment_no' => SORT_ASC])->limit(1)->one();
			$appointment_arr = $appointment->attributes;
		    if($appointment_arr['appointment_no'] == $appointment_no){
			    return Service::sendSucc();
		    }else{
			    return Service::sendError(20219, '该预约号不是当前第一位');
		    }
	    }elseif($op=='sms'){
	    	$appointment = Appointment::findOne(['expert_uuid'=>$uuid,'appointment_no'=>$appointment_no]);
	    	if($appointment){
			    $clinic = $appointment->clinicUu;
			    $content = "【".$clinic->attributes['name']."】您预约的会诊开始了，请您尽快打开会诊程序，谢谢！";

			    $userInfo = User::findOne(['uuid'=>$appointment->attributes['clinic_uuid']]);

			    $ret = Service::sendSms($userInfo->attributes['mobile'], $content);
			    echo $ret;
			    exit;
		    }else{
			    return Service::sendError(20202, '获取预约信息失败');
		    }

	    }else{
		    return Service::sendError(20218, '缺少参数');
	    }


    }

    public function actionTest()
    {
        $expert_uuid = '6c7ba7de-e5ab-460a-aaf6-da9c6f067fa4';
        $queryParam = Yii::$app->request->queryParams;


        $params['ExpertTimeSearch']['expert_uuid'] = $expert_uuid;
        $params['ExpertTimeSearch']['date'] = '2017-07-11';
        $params['ExpertTimeSearch']['is_order'] = 0;

        $timeSearch = new ExpertTimeSearch();
        $provider = $timeSearch->search($params, 100);
        $data = $provider->getModels();
        $result = array();
        if (count($data) > 0) {
            foreach ($data as $v) {
                $hour = $v->attributes['hour'];
                $desc = Yii::$app->params['time.' . $hour];
                $zone = $v->attributes['zone'];
                $hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
                $result[$desc][] = $hour . ':' . Yii::$app->params['zone.' . $zone . '.start'] . '-' . $hour . ':' . Yii::$app->params['zone.' . $zone . '.end'];

            }
        } else {
            return Service::sendError(20902, '无空闲时间');
        }

        var_dump($result);
        exit();
    }

    private function ordertime($expert_uuid, $start_time, $end_time, $appoint_no, $clinic_uuid)
    {

        $date = date('Y-m-d', $start_time);
        $use_set = array();

        for ($start_time; $start_time < $end_time; $start_time = $start_time + 1800) {
            if ($start_time % 3600 == 0) {
                $hour = date('h', $start_time);
                $use_set[(int)$hour][1] = 1;
            } else {
                $hour = date('h', $start_time);
                $use_set[(int)$hour][2] = 1;
            }
        }
        $cnt = 0;

        $new_oredr['is_order'] = 1;
        $new_oredr['clinic_uuid'] = $clinic_uuid;
        $new_oredr['order_no'] = $appoint_no;
        foreach ($use_set as $k_sets => $value_sets) {
            foreach ($value_sets as $k => $v) {
                $op_status = ExpertTime::updateAll($new_oredr,
                    ['expert_uuid' => $expert_uuid,
                        'date' => $date,
                        'hour' => $k_sets,
                        'zone' => $k]);

                if ($op_status > 0) {
                    $cnt++;
                }
            }
        }
        return $cnt;
    }

    private function cancelorder($appoint_no, $clinic)
    {
        $new_oredr['status'] = 3;
        $new_oredr['clinic_uuid'] = $clinic;
        $new_oredr['order_no'] = $appoint_no;
        $op_status = Appointment::updateAll($new_oredr,
            ['order_no' => $appoint_no,
                'clinic_uuid' => $clinic]);
        return $op_status;
    }

    private function checktime($expert_uuid, $start_time, $end_time, $clinic_uuid = null)
    {
    	if($start_time < time()){
		    return Service::sendError(20299, '不可预约已经过去的时间段');
	    }

        $date_start = date('Y-m-d', $start_time);
        $date_end = date('Y-m-d', $end_time);

        if ($date_start !== $date_end) {
            return Service::sendError(20299, '不可以跨天预约');
        }

        $use_set = array();

        for ($start_time; $start_time < $end_time; $start_time = $start_time + 1800) {
            if ($start_time % 3600 == 0) {
                $hour = date('H', $start_time);
                $use_set[(int)$hour][1] = 1;
            } else {
                $hour = date('H', $start_time);
                $use_set[(int)$hour][2] = 1;
            }
        }


//		if($clinic_uuid){
//			$times = ExpertTime::find()
//				->where(['and',
//						['expert_uuid'=>$expert_uuid,'date'=>$date_end],
//						['or',
//							['is_order'=>0],
//							['clinic_uuid'=>$clinic_uuid]]])
//				->all();
//		}else{
        $times = ExpertTime::find()
            ->where(['expert_uuid' => $expert_uuid, 'date' => $date_end, 'is_order' => 0])
            ->all();
//		}

        $free_time = array();
        if ($times) {
            foreach ($times as $v) {
                $temp = $v->attributes;
                $free_time[$temp['hour']][$temp['zone']] = 1;
                unset($temp);
            }
        }

        $arr_diff = $this->array_diff_assoc_recursive($use_set, $free_time);
        return $arr_diff;


    }


//多维数组的差集
    private function array_diff_assoc_recursive($array1, $array2)
    {
        $diffarray = array();
        foreach ($array1 as $key => $value) {
            //判断数组每个元素是否是数组
            if (is_array($value)) {
                //判断第二个数组是否存在key
                if (!isset($array2[$key])) {
                    $diffarray[$key] = $value;
                    //判断第二个数组key是否是一个数组
                } elseif (!is_array($array2[$key])) {
                    $diffarray[$key] = $value;
                } else {
                    $diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if ($diff != false) {
                        $diffarray[$key] = $diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $value !== $array2[$key]) {
                $diffarray[$key] = $value;
            }
        }
        return $diffarray;
    }

}
