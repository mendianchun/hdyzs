<?php
namespace api\modules\v1\controllers;

use yii;
use api\modules\ApiBaseController;
use common\models\SystemConfig;
use common\service\Service;
use yii\helpers\ArrayHelper;


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

    public function actionRecord($id)
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

        $postData = ['api_key' => $api_key, 'api_secret' => $api_secret, 'meeting_id' => $id, 'zcode' => 9496495821, 'from' => date("Y/m/d"), 'to' => date("Y/m/d")];

        $ret = Service::curl_post($postData, $url);
        echo $ret;
        exit;
    }

    public function actionGet($meeting_number,$zcode)
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

        $postData = ['api_key' => $api_key,'api_secret' => $api_secret,'zcode' => $zcode,'meeting_id'=>$meeting_number];
        $ret = Service::curl_post($postData, $url);
        echo $ret;
        exit;
    }

    public function actionEnd($meeting_number,$zcode)
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

        $postData = ['api_key' => $api_key,'api_secret' => $api_secret,'zcode' => $zcode,'meeting_id'=>$meeting_number];
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

    public function actionSendsms($mobile,$content)
    {
        $ret = Service::sendSms($mobile,$content);
        echo $ret;
        exit;
    }


}
