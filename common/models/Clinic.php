<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "clinic".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $tel
 * @property string $chief
 * @property string $idcard
 * @property string $Business_license_img
 * @property string $local_img
 * @property string $doctor_certificate_img
 * @property integer $score
 * @property integer $verify_status
 * @property string $user_uuid
 * @property string $verify_reason
 *
 * @property User $userUu
 * @property DrugCode[] $drugCodes
 * @property Order[] $orders
 * @property ScoreLog[] $scoreLogs
 */
class Clinic extends \yii\db\ActiveRecord
{
    //审核状态，1:待审核，2：审核通过，3：审核不通过
    const STATUS_WAITING = 1;
    const STATUS_SUCC = 2;
    const STATUS_FAILED = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'clinic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address', 'tel', 'chief', 'idcard', 'Business_license_img', 'local_img', 'doctor_certificate_img', 'user_uuid'], 'required'],
            [['score', 'verify_status'], 'integer'],
            [['verify_reason'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['address', 'Business_license_img', 'local_img', 'doctor_certificate_img'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 12],
            [['chief'], 'string', 'max' => 10],
            [['idcard'], 'string', 'max' => 18],
            [['user_uuid'], 'string', 'max' => 36],
            [['name'], 'unique'],
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
            'name' => '名字',
            'address' => '地址',
            'tel' => '联系电话',
            'chief' => '负责人',
            'idcard' => '身份证',
            'Business_license_img' => '营业许可证书',
            'local_img' => '诊所实景图像',
            'doctor_certificate_img' => '医师营业证书',
            'score' => '积分',
            'verify_status' => '认证状态',
            'user_uuid' => 'uuid',
            'verify_reason' => '不通过原因',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserUu()
    {
        return $this->hasOne(User::className(), ['uuid' => 'user_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDrugCodes()
    {
        return $this->hasMany(DrugCode::className(), ['clinic_uuid' => 'user_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['clinic_uuid' => 'user_uuid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScoreLogs()
    {
        return $this->hasMany(ScoreLog::className(), ['clinic_uuid' => 'user_uuid']);
    }

    public static function allStatus()
    {
        return [self::STATUS_WAITING=>'待审核',self::STATUS_SUCC=>'审核通过',self::STATUS_FAILED=>'审核不通过'];
    }

    public  function getStatusStr()
    {
        if($this->verify_status==self::STATUS_WAITING){
            return '待审核';
        }else if($this->verify_status==self::STATUS_SUCC){
            return '审核通过';
        }else{
            return '审核不通过';
        }
    }
}
