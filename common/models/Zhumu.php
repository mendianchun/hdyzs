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
 * @property integer $create_at
 * @property integer $update_at
 *
 * @property AppointmentVideo[] $appointmentVideos
 * @property ZhumuStatus $status0
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
            [['status', 'create_at', 'update_at'], 'integer'],
            [['uuid'], 'string', 'max' => 36],
            [['username', 'password'], 'string', 'max' => 100],
            [['uuid'], 'unique'],
            [['username'], 'unique'],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => ZhumuStatus::className(), 'targetAttribute' => ['status' => 'status']],
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
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointmentVideos()
    {
        return $this->hasMany(AppointmentVideo::className(), ['zhumu_uuid' => 'uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(ZhumuStatus::className(), ['status' => 'status']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->create_at = time();
                $this->update_at = time();
            } else {
                $this->update_at = time();
            }
            return true;
        } else {
            return false;
        }
    }
}
