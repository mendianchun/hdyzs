<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "drug_code".
 *
 * @property integer $id
 * @property string $code
 * @property string $info
 * @property integer $create_at
 * @property string $clinic_uuid
 * @property integer $submit_at
 *
 * @property Clinic $clinicUu
 */
class DrugCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'drug_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['info'], 'string'],
            [['create_at', 'submit_at'], 'integer'],
            [['code'], 'string', 'max' => 50],
            [['clinic_uuid'], 'string', 'max' => 36],
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
            'info' => '相信信息',
            'create_at' => '导入时间',
            'clinic_uuid' => '诊所uuid',
            'submit_at' => '诊所提交时间',
        ];
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
            $this->create_at = time();
            return true;
        } else {
            return false;
        }
    }
}
