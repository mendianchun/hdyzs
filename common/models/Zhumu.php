<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "zhumu".
 *
 * @property integer $id
 * @property string $uuid
 * @property string $appkey
 * @property string $appsecret
 * @property string $username
 * @property string $password
 * @property integer $status
 * @property integer $create_at
 * @property integer $update_at
 *
 * @property OrderVideo[] $orderVideos
 */
class Zhumu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zhumu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'appkey', 'appsecret', 'username', 'password', 'status', 'create_at', 'update_at'], 'required'],
            [['status', 'create_at', 'update_at'], 'integer'],
            [['uuid'], 'string', 'max' => 36],
            [['appkey'], 'string', 'max' => 20],
            [['appsecret', 'username', 'password'], 'string', 'max' => 100],
            [['uuid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'appkey' => 'Appkey',
            'appsecret' => 'Appsecret',
            'username' => 'Username',
            'password' => 'Password',
            'status' => 'Status',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderVideos()
    {
        return $this->hasMany(OrderVideo::className(), ['zhumu_uuid' => 'uuid']);
    }
}
