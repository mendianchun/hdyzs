<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "appointment".
 *
 * @property integer $appointment_no
 * @property string $clinic_uuid
 * @property string $expert_uuid
 * @property integer $order_starttime
 * @property integer $order_endtime
 * @property integer $order_fee
 * @property integer $real_starttime
 * @property integer $real_endtime
 * @property integer $real_fee
 * @property string $patient_name
 * @property integer $patient_age
 * @property string $patient_mobile
 * @property string $patient_idcard
 * @property string $patient_description
 * @property string $expert_diagnosis
 * @property integer $pay_type
 * @property integer $status
 * @property integer $pay_status
 * @property integer $is_sms_notify
 * @property integer $fee_type
 * @property integer $create_at
 * @property integer $update_at
 *
 * @property Clinic $clinicUu
 * @property Expert $expertUu
 * @property AppointmentVideo[] $appointmentVideos
 * @property Pay[] $pays
 */
class Appointment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'appointment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appointment_no', 'clinic_uuid', 'expert_uuid', 'order_starttime', 'order_endtime', 'patient_name', 'patient_age', 'patient_description', 'create_at', 'update_at'], 'required'],
            [['appointment_no', 'order_starttime', 'order_endtime', 'order_fee', 'real_starttime', 'real_endtime', 'real_fee', 'patient_age', 'pay_type', 'status', 'pay_status', 'is_sms_notify', 'fee_type', 'create_at', 'update_at'], 'integer'],
            [['patient_description', 'expert_diagnosis'], 'string'],
            [['clinic_uuid', 'expert_uuid'], 'string', 'max' => 36],
            [['patient_name'], 'string', 'max' => 10],
            [['patient_mobile'], 'string', 'max' => 11],
            [['patient_idcard'], 'string', 'max' => 18],
            [['clinic_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Clinic::className(), 'targetAttribute' => ['clinic_uuid' => 'user_uuid']],
            [['expert_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Expert::className(), 'targetAttribute' => ['expert_uuid' => 'user_uuid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'appointment_no' => 'Appointment No',
            'clinic_uuid' => 'Clinic Uuid',
            'expert_uuid' => 'Expert Uuid',
            'order_starttime' => 'Order Starttime',
            'order_endtime' => 'Order Endtime',
            'order_fee' => 'Order Fee',
            'real_starttime' => 'Real Starttime',
            'real_endtime' => 'Real Endtime',
            'real_fee' => 'Real Fee',
            'patient_name' => 'Patient Name',
            'patient_age' => 'Patient Age',
            'patient_mobile' => 'Patient Mobile',
            'patient_idcard' => 'Patient Idcard',
            'patient_description' => 'Patient Description',
            'expert_diagnosis' => 'Expert Diagnosis',
            'pay_type' => 'Pay Type',
            'status' => 'Status',
            'pay_status' => 'Pay Status',
            'is_sms_notify' => 'Is Sms Notify',
            'fee_type' => 'Fee Type',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicUu()
    {
        return $this->hasOne(Clinic::className(), ['user_uuid' => 'clinic_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpertUu()
    {
        return $this->hasOne(Expert::className(), ['user_uuid' => 'expert_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointmentVideos()
    {
        return $this->hasMany(AppointmentVideo::className(), ['appointment_no' => 'appointment_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPays()
    {
        return $this->hasMany(Pay::className(), ['appointment_no' => 'appointment_no']);
    }
}
