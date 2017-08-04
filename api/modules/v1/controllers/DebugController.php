<?php
namespace api\modules\v1\controllers;

use yii;
use api\modules\ApiBaseController;
use common\models\SystemConfig;
use common\service\Service;
use yii\helpers\ArrayHelper;
use dosamigos\qrcode\QrCode;    //引入类
use dosamigos\qrcode\lib\Enum;

class DebugController extends ApiBaseController
{
    public $modelClass = 'common\models\Zhumu';//对应的数据模型处理控制器

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                    'metrics',
                    'record',
                    'get',
                    'end',
                    'create',
                    'sendsms',
                    'qrcode',
                ],
            ]
        ]);
    }

    public function actionMetrics()
    {

        $url = 'https://api.zhumu.me/v3/meeting/metrics';

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
        if (isset($systemConfig)) {
            $api_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
        if (isset($systemConfig)) {
            $api_secret = $systemConfig['value'];
        }

        $postData = ['api_key' => $api_key, 'api_secret' => $api_secret, 'type' => 2, 'from' => date("Y/m/d", strtotime('-1 day')), 'to' => date("Y/m/d")];

        $ret = Service::curl_post($postData, $url);

        echo $ret;
        exit;
    }

    public function actionRecord($meeting_number, $zcode)
    {
        $url = 'https://api.zhumu.me/v3/meeting/mcrecording';

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
        if (isset($systemConfig)) {
            $api_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
        if (isset($systemConfig)) {
            $api_secret = $systemConfig['value'];
        }

        $postData = ['api_key' => $api_key, 'api_secret' => $api_secret, 'meeting_id' => $meeting_number, 'zcode' => $zcode, 'from' => date("Y/m/d"), 'to' => date("Y/m/d")];

        $ret = Service::curl_post($postData, $url);
        echo $ret;
        exit;
    }

    public function actionGet($meeting_number, $zcode)
    {
        $url = 'https://api.zhumu.me/v3/meeting/get';

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
        if (isset($systemConfig)) {
            $api_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
        if (isset($systemConfig)) {
            $api_secret = $systemConfig['value'];
        }

        $postData = ['api_key' => $api_key, 'api_secret' => $api_secret, 'zcode' => $zcode, 'meeting_id' => $meeting_number];
        $ret = Service::curl_post($postData, $url);
        echo $ret;
        exit;
    }

    public function actionEnd($meeting_number, $zcode)
    {
        $url = 'https://api.zhumu.me/v3/meeting/end';

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
        if (isset($systemConfig)) {
            $api_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
        if (isset($systemConfig)) {
            $api_secret = $systemConfig['value'];
        }

        $postData = ['api_key' => $api_key, 'api_secret' => $api_secret, 'zcode' => $zcode, 'meeting_id' => $meeting_number];
        $ret = Service::curl_post($postData, $url);
        echo $ret;
        exit;
    }

    public function actionCreate()
    {
        $url = 'https://api.zhumu.me/v3/meeting/end';

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
        if (isset($systemConfig)) {
            $api_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
        if (isset($systemConfig)) {
            $api_secret = $systemConfig['value'];
        }

        $postData = ['api_key' => $api_key, 'api_secret' => $api_secret, 'zcode' => 9496495821, 'topic' => "远程会诊" . time(), 'type' => 1];
        $ret = Service::curl_post($postData, Yii::$app->params['zhumu.createmeeting']);
        echo $ret;
        exit;
    }

    public function actionSendsms($mobile, $content)
    {
        $ret = Service::sendSms($mobile, $content);
        echo $ret;
        exit;
    }

    public function actionQrcode($id, $url)
    {
        $date = date('Y/md');
        $qrPath = 'qrcode/' . $date;
        $qrName = $id . '.png';

        $targetFolder = Yii::getAlias('@yii_base') . '/data/img/' . $qrPath;
        $file = new \yii\helpers\FileHelper();
        $file->createDirectory($targetFolder);

        $qrFile = rtrim($targetFolder, '/') . '/' . $qrName;

        QrCode::png($url,$qrFile,Enum::QR_ECLEVEL_L,4,2);    //调用二维码生成方法

        $qrUrl = rtrim(Yii::$app->params['domain'], '/') . '/' . $qrPath . '/' . $qrName;

        return Service::sendSucc($qrUrl);
    }


}
