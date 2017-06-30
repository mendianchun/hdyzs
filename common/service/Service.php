<?php
/**
 * Created by PhpStorm.
 * User: damen
 * Date: 2017/6/7
 * Time: 下午3:47
 */

namespace common\service;

use Yii;

class Service
{
    //生产UUID
    public static function create_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    //成功返回
    public static function sendSucc($data = [])
    {
        if (!is_array($data)) {
            return array($data);
        }
        return $data;
    }

    //失败返回
    public static function sendError($code = 0, $message = '')
    {
        return [
            'code' => $code,
            'message' => $message,
        ];
    }

    //生成六位验证码
    public static function createSmsCode()
    {
        return rand(100000, 999999);
    }

    public static function isEmail($email)
    {
        $mode = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        if (preg_match($mode, $email)) {
            return true;
        } else {
            return false;
        }
    }

    public static function sendSms($mobile, $content)
    {
        if(!$mobile || !$content)
            return false;
        //调用短信接口发送短信
        $status = 0; //发送成功

        $smsLog = new \common\models\SmsLog();
        $smsLog->mobile = $mobile;
        $smsLog->content = $content;
        $smsLog->status = $status;
        $smsLog->save();
        return $status;
    }
}