<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AppointmentVideo;

/**
 * AppointmentVideoSearch represents the model behind the search form about `common\models\AppointmentVideo`.
 */
class AppointmentVideoSearch extends AppointmentVideo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'appointment_no', 'meeting_number', 'status', 'create_at'], 'integer'],
            [['zhumu_uuid', 'audio_url'], 'safe'],
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
        $query = AppointmentVideo::find();

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
            'appointment_no' => $this->appointment_no,
            'meeting_number' => $this->meeting_number,
            'status' => $this->status,
            'create_at' => $this->create_at,
        ]);

        $query->andFilterWhere(['like', 'zhumu_uuid', $this->zhumu_uuid])
            ->andFilterWhere(['like', 'audio_url', $this->audio_url]);

        return $dataProvider;
    }
}
