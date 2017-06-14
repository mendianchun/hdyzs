<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pay".
 *
 * @property integer $pay_no
 * @property integer $appointment_no
 * @property string $type
 * @property integer $time
 * @property integer $create_at
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
            [['pay_no', 'appointment_no', 'type', 'time', 'create_at', 'status'], 'required'],
            [['pay_no', 'appointment_no', 'time', 'create_at', 'status'], 'integer'],
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
            'time' => 'Time',
            'create_at' => 'Create At',
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
