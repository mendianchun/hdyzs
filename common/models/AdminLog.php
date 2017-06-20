<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "admin_log".
 *
 * @property integer $id
 * @property string $route
 * @property string $description
 * @property integer $create_at
 * @property integer $user_id
 * @property integer $ip
 */
class AdminLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['create_at', 'user_id', 'ip'], 'required'],
            [['create_at', 'user_id', 'ip'], 'integer'],
            [['route'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route' => 'Route',
            'description' => 'Description',
            'create_at' => 'Create At',
            'user_id' => 'User ID',
            'ip' => 'Ip',
        ];
    }
}
