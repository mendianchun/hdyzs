<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "verifycode_cache".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $code
 * @property integer $expire_time
 */
class VerifycodeCache extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'verifycode_cache';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'code', 'expire_time'], 'required'],
            [['expire_time'], 'integer'],
            [['mobile'], 'string', 'max' => 11],
            [['code'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => 'Mobile',
            'code' => 'Code',
            'expire_time' => 'Expire Time',
        ];
    }
}
