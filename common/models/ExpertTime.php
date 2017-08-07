<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "expert_time".
 *
 * @property string $expert_uuid
 * @property string $date
 * @property integer $hour
 * @property integer $zone
 * @property integer $is_order
 * @property string $clinic_uuid
 * @property string $order_no
 * @property integer $status
 * @property string $reason
 */
class ExpertTime extends \yii\db\ActiveRecord
{

	//预约单状态 1:正常，2:专家取消
	//const STATUS_WAITING = 1;
	const STATUS_SUCC = 1;
	const STATUS_CANCLE = 2;

	//时间区间，1:  00-29，2: 30-59
	const TIME_ZONE_FRONT = 1;
	const TIME_ZONE_BOTTOM = 2;


	//预约单状态 1:正常，2:专家取消 3:专家被删除
	//const STATUS_WAITING = 1;
	const ORDER_STATUS_SUCC = 1;
	const ORDER_STATUS_FREE = 2;
	const ORDER_STATUS_DEL = 3;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expert_time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hour', 'zone', 'is_order','status'], 'integer'],
            [['expert_uuid', 'clinic_uuid'], 'string', 'max' => 36],
            [['date'], 'string', 'max' => 10],
            [['order_no'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'expert_uuid' => '专家ID',
            'date' => '日期',
            'hour' => '小时',
            'zone' => '区间',
            'is_order' => '是否预约',
            'clinic_uuid' => '诊所ID',
            'order_no' => '预约单号',
	        'status'=>'状态',
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
	public function getExpert()
	{
		return $this->hasOne(Expert::className(), ['user_uuid' => 'expert_uuid']);
	}
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAppointment()
	{
		return $this->hasOne(Appointment::className(), ['appointment_no' => 'order_no']);
	}



	public static function allStatus()
	{
		return [self::STATUS_SUCC=>'正常',self::STATUS_CANCLE=>'专家取消'];
	}

	public  function getStatusStr()
	{
		if($this->status==self::STATUS_SUCC){
			return '正常';
		}else if($this->status==self::STATUS_CANCLE){
			return '专家取消';
		}else{
			return '错误';
		}
	}


	public static function allZone()
	{
		return [self::TIME_ZONE_FRONT=>'00-29',self::TIME_ZONE_BOTTOM=>'30-59'];
	}

	public  function getZoneStr()
	{
		if($this->zone==self::TIME_ZONE_FRONT){
			return '00-29';
		}else if($this->zone==self::TIME_ZONE_BOTTOM){
			return '30-59';
		}else{
			return 'err';
		}
	}
	public static function allOrderStatus()
	{
		return [self::ORDER_STATUS_SUCC=>'已预约',self::ORDER_STATUS_FREE=>'空闲'];
	}

	public function  getOrderStatus()
	{
		if($this->order_no >0){
			return '已预约';
		}else{
			return '空闲';
		}
	}

}
