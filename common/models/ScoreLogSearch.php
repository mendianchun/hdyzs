<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ScoreLog;

/**
 * ScoreLogSearch represents the model behind the search form about `common\models\ScoreLog`.
 */
class ScoreLogSearch extends ScoreLog
{
    public function attributes()
    {
        return array_merge(parent::attributes(), ['clinicName']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'old_score', 'add_score', 'new_score', 'created_at'], 'integer'],
            [['clinic_uuid', 'reason', 'clinicName'], 'safe'],
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
        $query = ScoreLog::find();

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
            'old_score' => $this->old_score,
            'add_score' => $this->add_score,
            'new_score' => $this->new_score,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'clinic_uuid', $this->clinic_uuid])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        $query->join('INNER JOIN', 'clinic', 'score_log.clinic_uuid = clinic.user_uuid');
        $query->andFilterWhere(['like', 'clinic.name', $this->clinicName]);

        $dataProvider->sort->attributes['clinicName'] =
            [
                'asc' => ['clinic.name' => SORT_ASC],
                'desc' => ['clinic.name' => SORT_DESC],
            ];

        return $dataProvider;
    }
}
