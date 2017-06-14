<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pay".
 *
 * @property integer $pay_no
 * @property integer $order_no
 * @property string $type
 * @property integer $time
 * @property integer $create_at
 * @property integer $status
 *
 * @property Order $orderNo
 */
class Pay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_no', 'order_no', 'type', 'time', 'create_at', 'status'], 'required'],
            [['pay_no', 'order_no', 'time', 'create_at', 'status'], 'integer'],
            [['type'], 'string', 'max' => 10],
            [['order_no'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_no' => 'order_no']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pay_no' => 'Pay No',
            'order_no' => 'Order No',
            'type' => 'Type',
            'time' => 'Time',
            'create_at' => 'Create At',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderNo()
    {
        return $this->hasOne(Order::className(), ['order_no' => 'order_no']);
    }
}
