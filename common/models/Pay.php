<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pay".
 *
 * @property integer $pay_no
 * @property integer $appointment_no
 * @property string $type
 * @property integer $pay_time
 * @property integer $created_at
 * @property integer $status
 *
 * @property Appointment $appointmentNo
 */
class Pay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_no', 'appointment_no', 'type', 'pay_time', 'created_at'], 'required'],
            [['pay_no', 'appointment_no', 'pay_time', 'created_at', 'status'], 'integer'],
            [['type'], 'string', 'max' => 10],
            [['appointment_no'], 'exist', 'skipOnError' => true, 'targetClass' => Appointment::className(), 'targetAttribute' => ['appointment_no' => 'appointment_no']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pay_no' => 'Pay No',
            'appointment_no' => 'Appointment No',
            'type' => 'Type',
            'pay_time' => 'Pay Time',
            'created_at' => 'Create At',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointmentNo()
    {
        return $this->hasOne(Appointment::className(), ['appointment_no' => 'appointment_no']);
    }
}
