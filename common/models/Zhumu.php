<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "zhumu".
 *
 * @property integer $id
 * @property string $uuid
 * @property string $username
 * @property string $password
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AppointmentVideo[] $appointmentVideos
 */
class Zhumu extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_USED = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zhumu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['uuid'], 'string', 'max' => 36],
            [['username', 'password'], 'string', 'max' => 100],
            [['uuid'], 'unique'],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'username' => '用户名',
            'password' => '密码',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointmentVideos()
    {
        return $this->hasMany(AppointmentVideo::className(), ['zhumu_uuid' => 'uuid']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = time();
                $this->updated_at = time();
            } else {
                $this->updated_at = time();
            }
            return true;
        } else {
            return false;
        }
    }

    public static function allStatus()
    {
        return [self::STATUS_ACTIVE=>'正常',self::STATUS_USED=>'正在用',self::STATUS_DELETED=>'已删除'];
    }

    public  function getStatusStr()
    {
        if($this->status==self::STATUS_ACTIVE){
            return '正常';
        }else if($this->status==self::STATUS_USED){
            return '正在用';
        }else{
            return '已删除';
        }
    }
}
