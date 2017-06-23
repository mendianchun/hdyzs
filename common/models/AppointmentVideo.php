<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "appointment_video".
 *
 * @property integer $id
 * @property integer $appointment_no
 * @property string $zhumu_uuid
 * @property integer $meeting_number
 * @property string $audio_url
 * @property integer $status
 * @property integer $create_at
 *
 * @property Appointment $appointmentNo
 * @property Zhumu $zhumuUu
 */
class AppointmentVideo extends \yii\db\ActiveRecord
{
    //生成状态：1:未生成，2:生成中，3:生成失败，4:生成完成
    const STATUS_UNDO = 1;
    const STATUS_DOING = 2;
    const STATUS_FAILED = 3;
    const STATUS_FINISH = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointment_video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appointment_no', 'zhumu_uuid', 'create_at'], 'required'],
            [['appointment_no', 'meeting_number', 'status', 'create_at'], 'integer'],
            [['zhumu_uuid'], 'string', 'max' => 36],
            [['audio_url'], 'string', 'max' => 100],
            [['appointment_no'], 'exist', 'skipOnError' => true, 'targetClass' => Appointment::className(), 'targetAttribute' => ['appointment_no' => 'appointment_no']],
            [['zhumu_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Zhumu::className(), 'targetAttribute' => ['zhumu_uuid' => 'uuid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appointment_no' => 'Appointment No',
            'zhumu_uuid' => 'Zhumu Uuid',
            'meeting_number' => 'Meeting Number',
            'audio_url' => 'Audio Url',
            'status' => 'Status',
            'create_at' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointmentNo()
    {
        return $this->hasOne(Appointment::className(), ['appointment_no' => 'appointment_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZhumuUu()
    {
        return $this->hasOne(Zhumu::className(), ['uuid' => 'zhumu_uuid']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->create_at = time();
            } else {
            }
            return true;
        } else {
            return false;
        }
    }
}
