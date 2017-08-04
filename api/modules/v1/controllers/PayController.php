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
use dosamigos\qrcode\QrCode;
use dosamigos\qrcode\lib\Enum;

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

        //检查预约单支付状态，如果已经支付就不需要再支付了。
        if ($appointment->pay_status == Appointment::PAY_STATUS_PAYED) {
            return Service::sendError(20501, '该预约单已经支付');
        }

        if (!in_array($channel, Yii::$app->params['pay.channel'])) {
            return Service::sendError(20502, '支付渠道错误');
        }

        /*扫码支付方式，每次生成新的支付单，所以不检查是否有未支付的订单。
         * 检查是否有相关联的支付单
         * 如果有，并且已经支付，则更新预约单支付状态。
         * 如果有，并且没有支付，并且没有过期，则不需要创建新的支付单，继续提交该支付单。
         * 如果已经过期或者没有支付单，则创建新的支付单。
         */
//        $pays = Pay::findAll(['appointment_no' => $appointment_no]);
//        if (!empty($pays)) {
//            foreach ($pays as $pay) {
//                //支付单金额和预约单金额必须相符
//                if ($pay->amount != $appointment->order_fee * 100) {
//                    continue;
//                }
//
//                if ($pay->status == Pay::PAY_STATUS_PAYED) {
//                    //修改预约单状态为已支付状态
//                    $appointment->pay_status = Appointment::PAY_STATUS_PAYED;
//                    $appointment->pay_time = $pay->pay_time;
//                    $appointment->pay_type = Appointment::PAY_TYPE_ONLINE;
//                    $appointment->save();
//                    return Service::sendError(20501, '该预约单已经支付');
//                } elseif ($pay->outdated_at <= time()) {
//                    unset($pay);
//                    break;
//                }
//            }
//        }

        if (empty($pay)) {
            $pay_no = date("YmdHis") . rand(10000, 99999);
            $pay = new Pay();
            $pay->pay_no = $pay_no;
            $pay->appointment_no = $appointment_no;
            $pay->amount = $appointment->order_fee * 100;//分
            $pay->created_at = time();
            $pay->outdated_at = $pay->created_at + Yii::$app->params['pay.time'];
            $pay->channel = $channel;
            if (!$pay->save()) {
                return Service::sendError(20502, '创建支付单失败');
            }
        }

        Pingpp::setApiKey('sk_test_0WTSe58efrDCHWH0K4zrbvrD');
        $param = array('order_no' => $pay->pay_no,
            'amount' => $pay->amount,//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
            'app' => array('id' => 'app_TSmrT8H8uTe5zDmb'),
            'channel' => $channel,
            'currency' => 'cny',
            'client_ip' => '127.0.0.1',
            'subject' => 'fee',
            'body' => 'XXX',
        );

        if ($channel == 'wx_pub_qr') {
            $param['extra']['product_id'] = 1;
        }

        $charge = Charge::create($param);

        //扫码支付需要生成二维码，并返回二维码地址。其他方式直接输入charge对象。
        if (in_array($channel, ['alipay_qr', 'wx_pub_qr'])) {
            $chargeObj = json_decode($charge, true);
            if (isset($chargeObj['credential'][$channel]) && !empty($chargeObj['credential'][$channel])) {
                $url = $chargeObj['credential'][$channel];

                $date = date('Y/md');
                $qrPath = 'qrcode/' . $date;
                $qrName = $pay_no . '.png';

                $targetFolder = Yii::getAlias('@yii_base') . '/data/img/' . $qrPath;
                $file = new \yii\helpers\FileHelper();
                $file->createDirectory($targetFolder);

                $qrFile = rtrim($targetFolder, '/') . '/' . $qrName;

                QrCode::png($url, $qrFile, Enum::QR_ECLEVEL_L, 4, 2);    //调用二维码生成方法

                $qrUrl = rtrim(Yii::$app->params['domain'], '/') . '/' . $qrPath . '/' . $qrName;

                return Service::sendSucc($qrUrl);
            } else {
                return Service::sendError(20503, '支付平台返回错误');
            }
        } else {
            echo $charge;
            exit;
        }
    }

    /*
     * 支付成功后的回调
     */
    public function actionCallback()
    {
        $event = json_decode(file_get_contents("php://input"), true);
        $inputData = $event['data']['object'];
        switch ($event['type']) {
            case "charge.succeeded":
                if ($inputData['paid'] == true) {
                    $pay = Pay::findOne(['pay_no' => $inputData['id']]);
                    if ($pay) {

                        if ($pay->amount != $inputData['amount']) {
                            echo 'error';
                        } else {
                            //修改支付单状态
                            $pay->status = Pay::PAY_STATUS_PAYED;
                            $pay->pay_time = $inputData['time_paid'];
                            $pay->save();
                            //修改支付订单
                            $appointment = Appointment::findOne(['appointment_no' => $pay->appointment_no]);

                            if ($appointment) {
                                $appointment->pay_status = Appointment::PAY_STATUS_PAYED;
                                $appointment->pay_time = $inputData['time_paid'];
                                $appointment->pay_type = Appointment::PAY_TYPE_ONLINE;
                                $appointment->save();
                                echo 'success';
                            }
                        }
                    } else {
                        echo "error";
                    }
                }
                break;
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
        $params['AppointmentSearch']['dx_status'] = Appointment::DX_STATUS_DO;

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
        $data['start_day'] = date("Y-m-d", $user->created_at);
        $data['end_day'] = date('Y-m-d');
        $data['unpayAmount'] = Appointment::getUnpayAmount($uuid);
        $data['payedAmount'] = Appointment::getPayedAmount($uuid);

        return Service::sendSucc($data);
    }
}