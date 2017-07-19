<?php
namespace api\modules\v1\controllers;

use Composer\Downloader\ZipDownloader;
use yii;
use api\modules\ApiBaseController;
use common\models\Zhumu;
use common\models\SystemConfig;
use common\models\AppointmentVideo;
use common\models\Appointment;

use yii\helpers\ArrayHelper;
use common\service\Service;
use common\models\User;


class ZhumuController extends ApiBaseController
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
                ],
            ]
        ]);
    }

    /*
     * 废弃
     * 接口1:从瞩目池中随机选取一个未被使用的
     * 包括（瞩目uuid、appkey、appsecret、username、password）
     */
    public function actionGetuser()
    {
        //当前用户
        $user = \yii::$app->user->identity;

        //只有专家才能发起
        if ($user->type != User::USER_EXPERT) {
            return Service::sendError(20401, '非法请求，只能专家才能发起会议');
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_app_key']);
        if (isset($systemConfig)) {
            $app_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_app_secret']);
        if (isset($systemConfig)) {
            $app_secret = $systemConfig['value'];
        }

        //随机选择一个瞩目账号
        $maxId = Zhumu::find()->where(["status" => Zhumu::STATUS_ACTIVE])->max('id');
        $randId = rand(0, $maxId);

        $zhumu = Zhumu::find()->where(['>=', 'id', $randId])->andWhere(['status' => Zhumu::STATUS_ACTIVE])->one();
        $zhumuArray = $zhumu->attributes;

        unset($zhumuArray['id'], $zhumuArray['status'], $zhumuArray['created_at'], $zhumuArray['updated_at']);
        $zhumuArray['app_key'] = $app_key;
        $zhumuArray['app_secret'] = $app_secret;
        return Service::sendSucc($zhumuArray);
    }

    /*
     * 废弃
     * 接口2:回传会议信息（瞩目uuid+会议号+预约单号）
     */
    public function actionSetinfo()
    {
        //当前用户
        $user = \yii::$app->user->identity;

        $post = Yii::$app->request->post();

        //参数检查
        if (!isset($post['uuid']) || !isset($post['meeting_number']) || !isset($post['appointment_no'])) {
            return Service::sendError(10400, '缺少参数');
        }

        //只有专家才能发起
        if ($user->type != User::USER_EXPERT) {
            return Service::sendError(20401, '非法请求，只能专家才能发起会议');
        }

        //判断是否存在瞩目信息
        $zhumu = Zhumu::findOne(['uuid' => $post['uuid']]);
        if (empty($zhumu)) {
            return Service::sendError(20402, '没有这个账号信息');
        }

        //判断是否存在此预约单
        $appointment = Appointment::findone(['appointment_no' => $post['appointment_no']]);
        if (empty($appointment)) {
            return Service::sendError(20403, '没有此预约单');
        }

        $zhumu->status = Zhumu::STATUS_USED;
        $zhumu->save();

        $appointmentVideo = AppointmentVideo::find()
            ->where(['appointment_no' => $post['appointment_no']])
            ->andWhere(['zhumu_uuid' => $post['uuid']])
            ->andWhere(['meeting_number' => $post['meeting_number']])
            ->one();

        if (empty($appointmentVideo)) {
            $appointmentVideo = new AppointmentVideo();
            $appointmentVideo->appointment_no = $post['appointment_no'];
            $appointmentVideo->zhumu_uuid = $post['uuid'];
            $appointmentVideo->meeting_number = $post['meeting_number'];
//            $appointmentVideo->created_at = time();
            if (!$appointmentVideo->save()) {
                return Service::sendError(20404, '处理失败');
            }
        }
        return Service::sendSucc();
    }

    /*
     * 接口3:通过预约单号获取瞩目会议信息
     * 包括（appkey、appsecret、username、password、meeting room）
     */
    public function actionGetmeetingnumber($appointment_no)
    {
        $retData = [];
        $appointmentVideo = AppointmentVideo::find()->where(['appointment_no' => $appointment_no])->orderBy('id DESC')->one();
        if (!empty($appointmentVideo)) {
            $retData['meeting_number'] = (string)$appointmentVideo->meeting_number;

            $systemConfig = SystemConfig::findOne(['name' => 'zhumu_app_key']);
            if (isset($systemConfig)) {
                $retData['app_key'] = $systemConfig['value'];
            }

            $systemConfig = SystemConfig::findOne(['name' => 'zhumu_app_secret']);
            if (isset($systemConfig)) {
                $retData['app_secret'] = $systemConfig['value'];
            }

            //不需要用户名和密码
//            $zhumu = Zhumu::findOne(['uuid' => $appointmentVideo->zhumu_uuid]);
//            if (!empty($zhumu)) {
//                $retData['username'] = $zhumu->username;
//                $retData['password'] = $zhumu->password;
//            }
        }
        return Service::sendSucc($retData);
    }

    /*废弃
     * 接口4:通知会议开始，回传预约单号。
     * 记录对应的预约单实际开始时间
     */
    public function actionStart()
    {
        //当前用户
        $user = \yii::$app->user->identity;

        $post = Yii::$app->request->post();

        //参数检查
        if (!isset($post['appointment_no'])) {
            return Service::sendError(10400, '缺少参数');
        }

        //只有诊所才能发起
        if ($user->type != User::USER_CLINIC) {
            return Service::sendError(20405, '非法请求，只能诊所才能调用');
        }

        //判断是否存在此预约单
        $appointment = Appointment::findone(['appointment_no' => $post['appointment_no']]);
        if (empty($appointment)) {
            return Service::sendError(20403, '没有此预约单');
        }

        //检查预约单状态，只有预约成功的才可以记录开始时间，并且只能记录一次。
        if ($appointment->status != Appointment::STATUS_SUCC) {
            return Service::sendError(20406, '只有预约成功的才可以开始');
        }

        if ($appointment->real_starttime != 0) {
            return Service::sendError(20407, '预约单已经开始了');
        }

        $appointment->real_starttime = time();
        if (!$appointment->save()) {
            return Service::sendError(20408, '预约单开始失败');
        }
        return Service::sendSucc();
    }

    /*
     * 接口5:通知会议结束、回传预约单号
     * 记录对应的预约单实际结束时间
     * 对应的瞩目账号状态改为正常
     */
    public function actionEnd()
    {
        //当前用户
        $user = \yii::$app->user->identity;

        $post = Yii::$app->request->post();

        //参数检查
        if (!isset($post['appointment_no'])) {
            return Service::sendError(10400, '缺少参数');
        }

        //只有专家才能发起
        if ($user->type != User::USER_EXPERT) {
            return Service::sendError(20401, '非法请求，只能专家才能发起会议');
        }

        //判断是否存在此预约单
        $appointment = Appointment::findone(['appointment_no' => $post['appointment_no']]);
        if (empty($appointment)) {
            return Service::sendError(20403, '没有此预约单');
        }

        //检查预约单状态，只有预约成功并且已经开始的才可以结束。
        if ($appointment->status != Appointment::STATUS_SUCC || $appointment->real_starttime == 0) {
            return Service::sendError(20409, '只有预约成功并且已经开始的才可以结束');
        }

//        if ($appointment->dx_status == Appointment::DX_STATUS_DO) {
//            return Service::sendError(20410, '预约单已经结束了');
//        }

        if ($appointment->real_endtime == 0) {
            $appointment->real_endtime = time();
        }
        $appointment->dx_status = Appointment::DX_STATUS_DO;

        //按次收费的，真实价格与预约价格相等，积分也与预约积分相等
        if ($appointment->fee_type == Appointment::FEE_TYPE_TIMES) {
            $appointment->real_fee = $appointment->order_fee;
            $appointment->real_score = $appointment->order_score;
        } else {
            //按次计费的需要重新计算 toDO
        }

        if (!$appointment->save()) {
            return Service::sendError(20411, '预约单结束失败');
        }

        //对应的瞩目账号状态改为正常
        $appointmentVideo = AppointmentVideo::findAll(['appointment_no' => $post['appointment_no']]);
        if (!empty($appointmentVideo)) {
            foreach ($appointmentVideo as $k => $v) {
                $zhumu = Zhumu::findOne(['uuid' => $v->zhumu_uuid]);
                $zhumu->status = Zhumu::STATUS_ACTIVE;
                $zhumu->save();

                //通知瞩目结束会议
                $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
                if (isset($systemConfig)) {
                    $api_app_key = $systemConfig['value'];
                }

                $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
                if (isset($systemConfig)) {
                    $api_app_secret = $systemConfig['value'];
                }
                $postData = ['api_key' => $api_app_key, 'api_secret' => $api_app_secret, 'zcode' => $zhumu->zcode, 'meeting_id' => $v->meeting_number];
                $ret = Service::curl_post($postData, Yii::$app->params['zhumu.endmeeting']);
            }
        }

        return Service::sendSucc();
    }

    /*
    * 接口6:访问mp3资源
    */
    public function actionGetmp3($appointment_no)
    {
        //当前用户
        $user = \yii::$app->user->identity;

        if (empty($appointment_no)) {
            echo "预约单号为空";
            exit;
        }

        $appointment = Appointment::findOne(['appointment_no' => $appointment_no, 'clinic_uuid' => $user->uuid]);
        if (!$appointment) {
            echo "没有此预约单";
            exit;
        }

        if (!$appointment->audio_url || !is_file(Yii::$app->params['zhumu.basedir'] . "/" . $appointment->audio_url)) {
            echo "还未生成音频";
            exit;
        }

        $file = Yii::$app->params['zhumu.basedir'] . "/" . $appointment->audio_url;
        $file_size = filesize($file);
        $ranges = $this->getRange($file_size);

        $fp = fopen($file, "rb");

        if ($ranges != null) {
            if ($ranges['start'] > 0 || $ranges['end'] < $file_size) {
                header('HTTP/1.1 206 Partial Content');
            }

            header("Accept-Ranges: bytes");
//            header("Content-Type: audio/mpeg");
            header("Content-Type: audio/mp4a-latm");
            // 剩余长度
            header(sprintf('Content-Length: %u', $ranges['end'] - $ranges['start']));

            // range信息
            header(sprintf('Content-Range: bytes %s-%s/%s', $ranges['start'], $ranges['end'], $file_size));

            // fp指针跳到断点位置
            fseek($fp, sprintf('%u', $ranges['start']));
        } else {
            //下载文件需要用到的头
            header("Accept-Ranges: bytes");
//            header("Content-Type: audio/mpeg");
            header("Content-Type: audio/mp4a-latm");
            header("Content-Length: " . $file_size);
        }

        $buffer = 1024;
        $file_count = 0;
//        $fpw = fopen('echo.mp3.log','w');
        //向浏览器返回数据
        while (!feof($fp) && $file_count < $file_size) {
            $file_con = fread($fp, $buffer);
            $file_count += $buffer;
//            fwrite($fpw,ftell($fp)."|".$buffer."|".$file_count."|".$file_size."\n");
            echo $file_con;
        }
        fclose($fp);
        exit;
    }

    /*
     * 接口7 创建会议
     * 创建会议-》获取会议号-》更改瞩目账号状态-》建立会议号与预约单的关联关系
     */
    public function actionCreate()
    {
        $post = Yii::$app->request->post();

        //参数检查
        if (!isset($post['appointment_no'])) {
            return Service::sendError(10400, '缺少参数');
        }

        //当前用户
        $user = \yii::$app->user->identity;

        //只有专家才能发起
        if ($user->type != User::USER_EXPERT) {
            return Service::sendError(20401, '非法请求，只能专家才能发起会议');
        }

        //判断是否存在此预约单
        $appointment = Appointment::findone(['appointment_no' => $post['appointment_no']]);
        if (empty($appointment)) {
            return Service::sendError(20403, '没有此预约单');
        }

        //检查预约单状态，只有预约成功的才可以开始视频。
        if ($appointment->status != Appointment::STATUS_SUCC) {
            return Service::sendError(20406, '只有预约成功的才可以开始');
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_app_key']);
        if (isset($systemConfig)) {
            $app_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_app_secret']);
        if (isset($systemConfig)) {
            $app_secret = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_key']);
        if (isset($systemConfig)) {
            $api_app_key = $systemConfig['value'];
        }

        $systemConfig = SystemConfig::findOne(['name' => 'zhumu_api_app_secret']);
        if (isset($systemConfig)) {
            $api_app_secret = $systemConfig['value'];
        }

        //检查该预约单是否已经创建过会议，并且会议未结束
        $appointmentVideo = AppointmentVideo::find()->where(['appointment_no' => $post['appointment_no']])->orderBy('id DESC')->one();
        if (!empty($appointmentVideo)) {
            $zhumu = Zhumu::findOne(['uuid' => $appointmentVideo->zhumu_uuid]);
            if (!empty($zhumu)) {
                $retData['username'] = $zhumu->username;
                $retData['password'] = $zhumu->password;
            }
            //检查会议是否结束
            $postData = ['api_key' => $api_app_key, 'api_secret' => $api_app_secret, 'zcode' => $zhumu->zcode, 'meeting_id' => $appointmentVideo->meeting_number];
            $ret = Service::curl_post($postData, Yii::$app->params['zhumu.getmeeting']);
            if (is_string($ret)) {
                $retArr = json_decode($ret, true);
                if (!isset($retArr['code'])) {
                    $retData['meeting_number'] = (string)$appointmentVideo->meeting_number;
                    $retData['app_key'] = $app_key;
                    $retData['app_secret'] = $app_secret;
                    return Service::sendSucc($retData);
                }
            }
        }

        //随机选择一个瞩目账号
        $maxId = Zhumu::find()->where(["status" => Zhumu::STATUS_ACTIVE])->max('id');
        if ($maxId > 0) {
            $randId = rand(0, $maxId);

            $zhumu = Zhumu::find()->where(['>=', 'id', $randId])->andWhere(['status' => Zhumu::STATUS_ACTIVE])->one();
            if (!empty($zhumu)) {
                $zcode = $zhumu->zcode;
            }
        }

        if (empty($zcode)) {
            return Service::sendError(20412, '无可用的创建会议的账号');
        }

        //调用zhumu接口创建会议
        $postData = ['api_key' => $api_app_key, 'api_secret' => $api_app_secret, 'zcode' => $zcode, 'topic' => "远程会诊" . $post['appointment_no'], 'type' => 2];
        $ret = Service::curl_post($postData, Yii::$app->params['zhumu.createmeeting']);
        if (is_string($ret)) {
            $retArr = json_decode($ret, true);
            if (isset($retArr['id'])) {
                $zhumu->status = Zhumu::STATUS_USED;
                $zhumu->save();

                $appointmentVideo = new AppointmentVideo();
                $appointmentVideo->appointment_no = $post['appointment_no'];
                $appointmentVideo->zhumu_uuid = $zhumu->uuid;
                $appointmentVideo->meeting_number = $retArr['id'];
                if ($appointmentVideo->save()) {
                    //记录预约单实际开始时间
                    if ($appointment->real_starttime == 0) {
                        $appointment->real_starttime = time();
                        $appointment->save();
                    }

                    $retData = ['app_key' => $app_key, 'app_secret' => $app_secret, 'username' => $zhumu->username, 'password' => $zhumu->password, 'meeting_number' => (string)$retArr['id']];
                    return Service::sendSucc($retData);
                }
            }
        }

        return Service::sendError(20404, '处理失败');
    }

    /** 获取header range信息
     * @param  int $file_size 文件大小
     * @return Array
     */
    private function getRange($file_size)
    {
        if (isset($_SERVER['HTTP_RANGE']) && !empty($_SERVER['HTTP_RANGE'])) {
            $range = $_SERVER['HTTP_RANGE'];
            $range = preg_replace('/[\s|,].*/', '', $range);
            $range = explode('-', substr($range, 6));
            if (count($range) < 2) {
                $range[1] = $file_size;
            }
            $range = array_combine(array('start', 'end'), $range);
            if (empty($range['start'])) {
                $range['start'] = 0;
            }
            if (empty($range['end'])) {
                $range['end'] = $file_size;
            }
            return $range;
        }
        return null;
    }
}  