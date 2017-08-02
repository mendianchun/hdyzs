<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "drug_code".
 *
 * @property integer $id
 * @property string $code
 * @property integer $created_at
 *
 * @property DrugCodeClinic $drugCodeClinic
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
            [['created_at'], 'integer'],
            [['code'], 'checkCode'],
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
            'created_at' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDrugCodeClinic()
    {
        return $this->hasOne(DrugCodeClinic::className(), ['code' => 'code']);
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

    public function checkCode($attribute , $params){
        //必须是20位的数字
        if(strlen($this->$attribute) != 20 || !preg_match('/^\d+$/i', $this->$attribute)){
            $this->addError($attribute , '监管码必须是20位的数字');
        }
    }
}
