<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "zhumu_status".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 *
 * @property Zhumu[] $zhumus
 */
class ZhumuStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zhumu_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['status'], 'unique'],
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
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZhumus()
    {
        return $this->hasMany(Zhumu::className(), ['status' => 'status']);
    }
}
