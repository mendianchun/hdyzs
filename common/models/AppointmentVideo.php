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
 * @property integer $create_at
 *
 * @property Appointment $appointmentNo
 * @property Zhumu $zhumuUu
 */
class AppointmentVideo extends \yii\db\ActiveRecord
{
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
            [['appointment_no', 'zhumu_uuid', 'meeting_number', 'audio_url', 'create_at'], 'required'],
            [['appointment_no', 'meeting_number', 'create_at'], 'integer'],
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
}
