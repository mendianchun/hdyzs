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
 *
 * @property User $userUu
 * @property DrugCode[] $drugCodes
 * @property Order[] $orders
 * @property ScoreLog[] $scoreLogs
 */
class Clinic extends \yii\db\ActiveRecord
{
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
            [['name'], 'string', 'max' => 100],
            [['address', 'Business_license_img', 'local_img', 'doctor_certificate_img'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 12],
            [['chief'], 'string', 'max' => 10],
            [['idcard'], 'string', 'max' => 18],
            [['user_uuid'], 'string', 'max' => 36],
            [['name'], 'unique'],
            [['idcard'], 'unique'],
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
            'name' => 'Name',
            'address' => 'Address',
            'tel' => 'Tel',
            'chief' => 'Chief',
            'idcard' => 'Idcard',
            'Business_license_img' => 'Business License Img',
            'local_img' => 'Local Img',
            'doctor_certificate_img' => 'Doctor Certificate Img',
            'score' => 'Score',
            'verify_status' => 'Verify Status',
            'user_uuid' => 'User Uuid',
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
}
