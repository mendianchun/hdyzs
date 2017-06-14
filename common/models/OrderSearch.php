<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderSearch represents the model behind the search form about `common\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'order_starttime', 'order_endtime', 'order_fee', 'real_starttime', 'real_endtime', 'real_fee', 'patient_age', 'pay_type', 'status', 'pay_status', 'is_sms_notify', 'fee_type', 'create_at', 'update_at'], 'integer'],
            [['clinic_uuid', 'expert_uuid', 'patient_name', 'patient_mobile', 'patient_idcard', 'patient_description', 'expert_diagnosis'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'order_no' => $this->order_no,
            'order_starttime' => $this->order_starttime,
            'order_endtime' => $this->order_endtime,
            'order_fee' => $this->order_fee,
            'real_starttime' => $this->real_starttime,
            'real_endtime' => $this->real_endtime,
            'real_fee' => $this->real_fee,
            'patient_age' => $this->patient_age,
            'pay_type' => $this->pay_type,
            'status' => $this->status,
            'pay_status' => $this->pay_status,
            'is_sms_notify' => $this->is_sms_notify,
            'fee_type' => $this->fee_type,
            'create_at' => $this->create_at,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'clinic_uuid', $this->clinic_uuid])
            ->andFilterWhere(['like', 'expert_uuid', $this->expert_uuid])
            ->andFilterWhere(['like', 'patient_name', $this->patient_name])
            ->andFilterWhere(['like', 'patient_mobile', $this->patient_mobile])
            ->andFilterWhere(['like', 'patient_idcard', $this->patient_idcard])
            ->andFilterWhere(['like', 'patient_description', $this->patient_description])
            ->andFilterWhere(['like', 'expert_diagnosis', $this->expert_diagnosis]);

        return $dataProvider;
    }
}
