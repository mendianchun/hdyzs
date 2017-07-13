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
 * @property integer $order_score
 * @property integer $real_starttime
 * @property integer $real_endtime
 * @property integer $real_fee
 * @property integer $real_score
 * @property string $patient_name
 * @property integer $patient_age
 * @property integer $patient_gender
 * @property string $patient_mobile
 * @property string $patient_idcard
 * @property string $patient_img1
 * @property string $patient_img2
 * @property string $patient_img3
 * @property string $patient_description
 * @property string $expert_diagnosis
 * @property integer $pay_type
 * @property integer $status
 * @property string $cancel_reason
 * @property integer $pay_status
 * @property integer $dx_status
 * @property integer $is_sms_notify
 * @property integer $fee_type
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $audio_url
 * @property integer $audio_created_at
 * @property integer $audio_status
 *
 * @property Clinic $clinicUu
 * @property Expert $expertUu
 * @property AppointmentVideo[] $appointmentVideos
 * @property Pay[] $pays
 */
class Appointment extends \yii\db\ActiveRecord
{
    //预约单状态 1:预约中，2：预约成功，3:预约取消 4:平台取消
    const STATUS_WAITING = 1;
    const STATUS_SUCC = 2;
    const STATUS_CANCLE = 3;
    const STATUS_EXPERT_CANCLE = 4;

    //支付状态，0:待支付，1:已支付
    const PAY_STATUS_UNPAY = 0;
    const PAY_STATUS_PAYED = 1;

    //支付方式：1:积分支付，2:线下支付，3:线上支付
    const PAY_TYPE_SCORE = 1;
    const PAY_TYPE_OFFLINE = 2;
    const PAY_TYPE_ONLINE = 3;

    //计费方式，1:按次，2:按小时
    const FEE_TYPE_TIMES = 1;
    const FEE_TYPE_HOURS = 2;

    //诊断状态，1:未诊断，2:已诊断
    const DX_STATUS_UN = 1;
    const DX_STATUS_DO = 2;

    //音频生成状态
    const AUDIO_STATUS_UNDO = 1;
    const AUDIO_STATUS_DONING = 2;
    const AUDIO_STATUS_FAILED = 3;
    const AUDIO_STATUS_SUCC = 4;

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
            [['appointment_no', 'clinic_uuid', 'expert_uuid', 'order_starttime', 'order_endtime', 'patient_name', 'patient_description', 'created_at', 'updated_at'], 'required'],
            [['appointment_no', 'order_starttime', 'order_endtime', 'order_fee', 'order_score', 'real_starttime', 'real_endtime', 'real_fee', 'real_score', 'patient_age', 'patient_gender', 'pay_type', 'status', 'pay_status', 'dx_status', 'is_sms_notify', 'fee_type', 'created_at', 'updated_at', 'audio_created_at', 'audio_status'], 'integer'],

            [['patient_description', 'expert_diagnosis'], 'string'],
            [['clinic_uuid', 'expert_uuid'], 'string', 'max' => 36],
            [['patient_name'], 'string', 'max' => 10],
            [['patient_mobile'], 'string', 'max' => 11],
            [['patient_idcard'], 'string', 'max' => 18],

            [['patient_img1', 'patient_img2', 'patient_img3'], 'string', 'max' => 255],
            [['cancel_reason'], 'string', 'max' => 40],
            [['audio_url'], 'string', 'max' => 255],

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
            'appointment_no' => '预约单号',
            'clinic_uuid' => '诊所uuid',
            'expert_uuid' => '专家uuid',
            'order_starttime' => '预约开始时间',
            'order_endtime' => '预约结束时间',
            'order_fee' => '预约价格',
            'order_score' => '预约积分',
            'real_starttime' => '实际开始时间',
            'real_endtime' => '实际结束时间',
            'real_fee' => '真实价格',
            'real_score' => '真实积分',
            'patient_name' => '患者名称',
            'patient_age' => '患者年龄',
            'patient_mobile' => '患者手机号',
            'patient_idcard' => '患者身份证号',
            'patient_description' => '患者主述',
            'expert_diagnosis' => '医生诊断',
            'pay_type' => '支付类型',
            'status' => '状态',
            'pay_status' => '支付状态',
            'dx_status' => '诊断状态',
            'is_sms_notify' => '是否短信通知患者',
            'fee_type' => '付费类型',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
	        'cancel_reason' => '预约取消原因',
	        'audio_url' => '诊断音频',
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

	public static function allStatus()
	{
		return [self::STATUS_CANCLE => '取消', self::STATUS_WAITING => '预约中', self::STATUS_SUCC => '预约成功'];
	}

	public function getStatusStr()
	{
		if ($this->status == self::STATUS_CANCLE) {
			return '取消';
		} else if ($this->status == self::STATUS_WAITING) {
			return '预约中';
		} else {
			return '预约成功';
		}
	}


	public static function allDxStatus()
	{
		return [self::DX_STATUS_UN => '未诊断', self::DX_STATUS_DO => '已诊断'];
	}
	public function getDxStatusStr()
	{
		if ($this->dx_status == self::DX_STATUS_UN) {
			return '未诊断';
		} else if ($this->dx_status == self::DX_STATUS_DO) {
			return '已诊断';
		}
	}

    public static function allPayStatus()
    {
        return [self::PAY_STATUS_UNPAY => '待支付', self::PAY_STATUS_PAYED => '已支付'];
    }

    public function getPayStatusStr()
    {
        return $this->pay_status == self::PAY_STATUS_PAYED ? '已支付' : '待支付';
    }

    public static function allPayTypeStatus()
    {
        return [self::PAY_TYPE_SCORE => '积分支付', self::PAY_TYPE_OFFLINE => '线下支付', self::PAY_TYPE_ONLINE => '线上支付'];
    }

    public function getPayTypeStatusStr()
    {
        if ($this->pay_type == self::PAY_TYPE_SCORE) {
            return '积分支付';
        } else if ($this->pay_type == self::PAY_TYPE_OFFLINE) {
            return '线下支付';
        } else {
            return '线上支付';
        }
    }

    public static function allFeeTypeStatus()
    {
        return [self::FEE_TYPE_TIMES => '按次', self::FEE_TYPE_HOURS => '按小时'];
    }

    public function getFeeTypeStatusStr()
    {
        return $this->fee_type == self::FEE_TYPE_TIMES ? '按次' : '按小时';
    }

    public function approve()
    {
        if ($this->status == self::STATUS_WAITING) {
            $this->status = self::STATUS_SUCC; //设置预约单状态为预约成功
            return ($this->save() ? true : false);
        }
        return true;
    }

    public function pay()
    {
        //只有预约成功的才能修改支付状态
        if ($this->status == self::STATUS_SUCC) {
            $this->pay_status = self::PAY_STATUS_PAYED; //设置预约单支付状态为支付成功
            return ($this->save() ? true : false);
        }
        return true;
    }

    /*
     * 获取待处理的数量
     */
    public static function getPengdingCount()
    {
        return Appointment::find()->where(['status' => self::STATUS_WAITING])->count();
    }

    public static function getUnpayAmount($clinic_uuid)
    {
        return Appointment::find()->where(['clinic_uuid' => $clinic_uuid, 'status' => self::STATUS_SUCC, 'pay_status' => self::PAY_STATUS_UNPAY, 'dx_status' => self::DX_STATUS_DO])->sum('real_fee');
    }

    public static function getPayedAmount($clinic_uuid)
    {
        return Appointment::find()->where(['clinic_uuid' => $clinic_uuid, 'status' => self::STATUS_SUCC, 'pay_status' => self::PAY_STATUS_PAYED, 'dx_status' => self::DX_STATUS_DO])->sum('real_fee');
    }
}
