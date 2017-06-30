<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ExpertTime;

/**
 * ExpertTimeSearch represents the model behind the search form about `common\models\ExpertTime`.
 */
class ExpertTimeSearch extends ExpertTime
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hour', 'zone', 'is_order', 'status'], 'integer'],
            [['expert_uuid', 'date', 'clinic_uuid', 'order_no', 'reason'], 'safe'],
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
        $query = ExpertTime::find();

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
            'id' => $this->id,
            'hour' => $this->hour,
            'zone' => $this->zone,
            'is_order' => $this->is_order,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'expert_uuid', $this->expert_uuid])
            ->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'clinic_uuid', $this->clinic_uuid])
            ->andFilterWhere(['like', 'order_no', $this->order_no])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }
}
