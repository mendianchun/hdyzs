<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_video".
 *
 * @property integer $id
 * @property integer $order_no
 * @property string $zhumu_uuid
 * @property integer $meeting_number
 * @property string $audio_url
 * @property integer $create_at
 *
 * @property Order $orderNo
 * @property Zhumu $zhumuUu
 */
class OrderVideo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_video';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'zhumu_uuid', 'meeting_number', 'audio_url', 'create_at'], 'required'],
            [['order_no', 'meeting_number', 'create_at'], 'integer'],
            [['zhumu_uuid'], 'string', 'max' => 36],
            [['audio_url'], 'string', 'max' => 100],
            [['order_no'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_no' => 'order_no']],
            [['zhumu_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Zhumu::className(), 'targetAttribute' => ['zhumu_uuid' => 'uuid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_no' => 'Order No',
            'zhumu_uuid' => 'Zhumu Uuid',
            'meeting_number' => 'Meeting Number',
            'audio_url' => 'Audio Url',
            'create_at' => 'Create At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderNo()
    {
        return $this->hasOne(Order::className(), ['order_no' => 'order_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZhumuUu()
    {
        return $this->hasOne(Zhumu::className(), ['uuid' => 'zhumu_uuid']);
    }
}
