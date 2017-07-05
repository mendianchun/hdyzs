<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "drug_code_clinic".
 *
 * @property integer $id
 * @property string $code
 * @property string $clinic_uuid
 * @property integer $created_at
 *
 * @property DrugCode $code0
 * @property Clinic $clinicUu
 */
class DrugCodeClinic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'drug_code_clinic';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'clinic_uuid'], 'required'],
            [['created_at'], 'integer'],
            [['code'], 'string', 'max' => 20],
            [['clinic_uuid'], 'string', 'max' => 36],
            [['code'], 'unique'],
            [['code'], 'exist', 'skipOnError' => true, 'targetClass' => DrugCode::className(), 'targetAttribute' => ['code' => 'code']],
            [['clinic_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Clinic::className(), 'targetAttribute' => ['clinic_uuid' => 'user_uuid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '药品监管码',
            'clinic_uuid' => '诊所uuid',
            'created_at' => '提交时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCode0()
    {
        return $this->hasOne(DrugCode::className(), ['code' => 'code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicUu()
    {
        return $this->hasOne(Clinic::className(), ['user_uuid' => 'clinic_uuid']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->created_at = time();
            return true;
        } else {
            return false;
        }
    }
}
