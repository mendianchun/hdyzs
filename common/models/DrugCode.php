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
            [['code', 'create_at', 'clinic_uuid'], 'required'],
            [['info'], 'string'],
            [['create_at'], 'integer'],
            [['code'], 'string', 'max' => 50],
            [['clinic_uuid'], 'string', 'max' => 36],
            [['code'], 'unique'],
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
            'code' => 'Code',
            'info' => 'Info',
            'create_at' => 'Create At',
            'clinic_uuid' => 'Clinic Uuid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicUu()
    {
        return $this->hasOne(Clinic::className(), ['user_uuid' => 'clinic_uuid']);
    }
}
