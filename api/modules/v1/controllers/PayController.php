<?php
/**
 * Created by PhpStorm.
 * User: damen
 * Date: 2017/6/12
 * Time: 下午2:16
 */
namespace api\modules\v1\controllers;

use common\models\Appointment;
use common\models\AppointmentSearch;
use common\models\Pay;
use common\service\Service;
use Pingpp\Charge;
use Pingpp\Pingpp;
use Yii;

use api\modules\ApiBaseController;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;

class PayController extends ApiBaseController
{


    public $modelClass = 'common\models\Pay';//对应的数据模型处理控制器


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                    'pingxx',
                    'create',
                ],
            ]
        ]);
    }

    public function actionCreate($appointment_no)
    {
//        $post = Yii::$app->request->post();
//        $apponitment_no = $post['appointment_no'];

        $appointment = Appointment::findOne(['appointment_no' => $appointment_no]);
//        var_dump($appointment);exit;
        $ret = $appointment->attributes;
//        var_dump($ret);exit;
        return Service::sendSucc($ret);

    }

    /**
     * pingxx支付
     */
    public function actionPingxx()
    {
        $post = Yii::$app->request->post();
        $appointment_no = $post['appointment_no'];
        $channel = $post['channel'];

        $appointment = Appointment::findOne(['appointment_no' => $appointment_no]);

        $pay = new Pay();
        $pay->pay_no = date("YmdHis") . rand(10000, 99999);
        $pay->appointment_no = $appointment_no;
        $pay->amount = $appointment->order_fee * 100;//分
        $pay->created_at = time();
        $pay->outdated_at = strtotime("+15 minutes");
        if ($pay->save()) {
            Pingpp::setApiKey('sk_test_0WTSe58efrDCHWH0K4zrbvrD');
            $charge = Charge::create(array('order_no' => $pay->pay_no,
                    'amount' => $pay->amount,//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                    'app' => array('id' => 'app_TSmrT8H8uTe5zDmb'),
                    'channel' => $channel,
                    'currency' => 'cny',
                    'client_ip' => '127.0.0.1',
                    'subject' => 'fee',
                    'body' => 'XXX',
                    'extra' => array(
                        'success_url' => 'http://api.hdyzs.com/pingpp/demo/views/success.html',
                    ))
            );

            echo($charge);
        }
        exit;
    }

    /*
     * 支付列表
     */
    public function actionList()
    {
        $result = array();

        $queryParam = Yii::$app->request->queryParams;
        $pageSize = isset($queryParam['size']) ? $queryParam['size'] : Yii::$app->params['list.pagesize'];

        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;
        if (isset($queryParam['type']) && $queryParam['type'] != 2) {
            $params['AppointmentSearch']['pay_status'] = ($queryParam['type'] == 0) ? 0 : 1;
        }
        $params['AppointmentSearch']['clinic_uuid'] = $uuid;
        $params['AppointmentSearch']['status'] = Appointment::STATUS_SUCC;
        $params['AppointmentSearch']['real_endtime'] = 0;

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
                $app['real_starttime'] = date('Y-m-d h:i', $item->attributes['real_starttime']);
                $app['real_endtime'] = date('Y-m-d h:i', $item->attributes['real_endtime']);
                //专家信息
                $expert = $item->expertUu;
                $app['expert_name'] = $expert->attributes['name'];
                $app['patient_name'] = $item->attributes['patient_name'];
                $app['pay_type'] = $item->attributes['pay_type'];
                $app['pay_status'] = $item->attributes['pay_status'];
                $app['real_fee'] = $item->attributes['real_fee'];
                $result[] = $app;
            }

            $result['extraFields']['currentPage'] = $currentPage;
            $result['extraFields']['totalCount'] = $provider->totalCount;
            $result['extraFields']['totalPage'] = $totalPage;
        }
        return Service::sendSucc($result);
    }

    public function actionTotal()
    {
        $user = \yii::$app->user->identity;
        $uuid = $user->uuid;

        $data = array();
        $data['start_day'] = date("Y-m-d",$user->created_at);
        $data['end_day'] =date('Y-m-d');
        $data['unpayAmount'] = Appointment::getUnpayAmount($uuid);
        $data['payedAmount'] = Appointment::getPayedAmount($uuid);

        return Service::sendSucc($data);
    }
}