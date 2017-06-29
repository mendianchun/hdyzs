<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "expert_time".
 *
 * @property string $expert_uuid
 * @property string $date
 * @property integer $hour
 * @property integer $zone
 * @property integer $is_order
 * @property string $clinic_uuid
 * @property string $order_no
 * @property integer $status
 * @property string $reason
 */
class ExpertTime extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expert_time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hour', 'zone', 'is_order'], 'integer'],
            [['expert_uuid', 'clinic_uuid'], 'string', 'max' => 36],
            [['date'], 'string', 'max' => 10],
            [['order_no'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'expert_uuid' => 'Expert Uuid',
            'date' => 'Date',
            'hour' => 'Hour',
            'zone' => 'Zone',
            'is_order' => 'Is Order',
            'clinic_uuid' => 'Clinic Uuid',
            'order_no' => 'Order No',
        ];
    }
}
