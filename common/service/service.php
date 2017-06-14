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
}