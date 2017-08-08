<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "expert".
 *
 * @property integer $id
 * @property string $name
 * @property string $head_img
 * @property string $free_time
 * @property integer $fee_per_times
 * @property integer $fee_per_hour
 * @property string $skill
 * @property string $introduction
 * @property string $url
 * @property string $user_uuid
 * @property string $expert_status
 *
 * @property Appointment[] $appointments
 * @property User $userUu
 */
class Expert extends \yii\db\ActiveRecord
{
	//专家状态
	const EXPERT_ON =1;
	const EXPERT_OFF =2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expert';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'head_img', 'user_uuid'], 'required'],
            [['free_time', 'introduction'], 'string'],
            [['fee_per_times', 'fee_per_hour'], 'integer'],
            [['name'], 'string', 'max' => 10],
            [['head_img'], 'string', 'max' => 100],
	        [['skill'], 'string', 'max' => 255],
	        [['introduction'], 'string', 'max' => 200],
	        [['url'], 'string', 'max' => 200],
	        [['expert_status'], 'number', 'max' => 200],
            [['user_uuid'], 'string', 'max' => 36],
            [['user_uuid'], 'unique'],
            [['user_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_uuid' => 'uuid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
	        'name' => '专家姓名',
//	        'loginname' => 'LoginName',
//	        'pwd' => 'pwd',
//	        'pwd_repeat' => 'pwd_repeat',
            'head_img' => '头像',
            'free_time' => '空闲时间',
            'fee_per_times' => '每次费用',
            'fee_per_hour' => '每小时费用',
            'skill' => '特长',
	        'introduction' => '介绍',
	        'url' => '链接',
            'user_uuid' => 'User Uuid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointments()
    {
        return $this->hasMany(Appointment::className(), ['expert_uuid' => 'user_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserUu()
    {
        return $this->hasOne(User::className(), ['uuid' => 'user_uuid']);
    }

    public function getExpertCost($expert_uuid)
    {
    	$expert =  self::findOne(['user_uuid' => $expert_uuid]);
    	$con = SystemConfig::findOne(['name'=>'fee2score']);
	    $result['score'] = $expert->attributes['fee_per_times']*$con->attributes['value'];
	    $result['fee'] = $expert->attributes['fee_per_times'];
	    return $result;
    }
}
