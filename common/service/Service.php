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
        if (!$mobile || !$content)
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

    public function curl_post($curlPost, $url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        if (is_array($curlPost)) {
            $curlPost = http_build_query($curlPost);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }

    public function curl_get($url)
    {
        $curl = curl_init();
        //设置选项，包括URL
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($curl);
        //释放curl句柄
        curl_close($curl);
        //打印获得的数据
        return $output;
    }

    public function download($url, $file)
    {
        if (empty($url) || empty($file))
            return false;
        if ($fp = fopen($url, 'r')) {
            if ($myfile = fopen($file, "w")) {
                while (!feof($fp)) {
                    fwrite($myfile, fgets($fp) . "");
                }
                fclose($fp);
                fclose($myfile);
            } else {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

}