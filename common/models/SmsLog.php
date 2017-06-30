<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sms_log".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $content
 * @property integer $created_at
 * @property integer $status
 *
 */
class SmsLog extends \yii\db\ActiveRecord
{
    //短信发送状态 0:成功，其他标示失败
    const STATUS_SUCC = 0;
    const STATUS_FAILED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'content'], 'required'],
            [['created_at', 'status'], 'integer'],
            [['mobile'], 'string', 'max' => 11],
            [['content'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => '手机号',
            'content' => '内容',
            'created_at' => '发送时间',
            'status' => '状态',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->created_at = time();
            return true;
        } else {
            return false;
        }
    }

    public static function allStatus()
    {
        return [self::STATUS_SUCC=>'成功',self::STATUS_FAILED=>'失败'];
    }

    public  function getStatusStr()
    {
        if($this->status==self::STATUS_SUCC){
            return '成功';
        }else{
            return '失败';
        }
    }
}
