<?php
/**
 * Created by PhpStorm.
 * User: damen
 * Date: 2017/6/12
 * Time: 下午2:16
 */
namespace api\modules\v1\controllers;

use common\models\Appointment;
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


    public $modelClass = 'common\models\User';//对应的数据模型处理控制器


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

        $appointment = Appointment::findOne(['appointment_no'=>$appointment_no]);
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

        $appointment = Appointment::findOne(['appointment_no'=>$appointment_no]);

        $pay = new Pay();
        $pay->pay_no = date("YmdHis").rand(10000,99999);
        $pay->appointment_no = $appointment_no;
        $pay->amount = $appointment->order_fee * 100;//分
        $pay->created_at = time();
        $pay->outdated_at = strtotime("+15 minutes");
        if($pay->save())
        {
            Pingpp::setApiKey('sk_test_0WTSe58efrDCHWH0K4zrbvrD');
            $charge = Charge::create(array('order_no' => $pay->pay_no,
                    'amount' => $pay->amount,//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                    'app' => array('id' => 'app_TSmrT8H8uTe5zDmb'),
                    'channel' => $channel,
                    'currency' => 'cny',
                    'client_ip' => '127.0.0.1',
                    'subject' => 'fee',
                    'body' => 'XXX',
                    'extra'=>array(
                        'success_url' => 'http://api.hdyzs.com/pingpp/demo/views/success.html',
                    ))
            );

            echo($charge);
        }
        exit;
    }
}