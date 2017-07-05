<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Pay;

/**
 * PaySearch represents the model behind the search form about `common\models\Pay`.
 */
class PaySearch extends Pay
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_no', 'appointment_no', 'time', 'created_at', 'status'], 'integer'],
            [['type'], 'safe'],
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
        $query = Pay::find();

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
            'pay_no' => $this->pay_no,
            'appointment_no' => $this->appointment_no,
            'time' => $this->time,
            'created_at' => $this->created_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
