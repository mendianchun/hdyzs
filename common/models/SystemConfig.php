<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "system_config".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 */
class SystemConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'system_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['id'], 'integer'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '配置项名称',
            'value' => '配置项值',
        ];
    }
}
