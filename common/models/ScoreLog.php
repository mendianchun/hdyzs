<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "score_log".
 *
 * @property integer $id
 * @property string $clinic_uuid
 * @property integer $old_score
 * @property integer $add_score
 * @property integer $new_score
 * @property string $reason
 * @property integer $created_at
 *
 * @property Clinic $clinicUu
 */
class ScoreLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'score_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['clinic_uuid', 'old_score', 'add_score', 'new_score', 'reason', 'created_at'], 'required'],
            [['old_score', 'add_score', 'new_score', 'created_at'], 'integer'],
            [['reason'], 'string'],
            [['clinic_uuid'], 'string', 'max' => 36],
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
            'clinic_uuid' => 'Clinic Uuid',
            'old_score' => 'Old Score',
            'add_score' => 'Add Score',
            'new_score' => 'New Score',
            'reason' => 'Reason',
            'created_at' => 'Created At',
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
