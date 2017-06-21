<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Clinic;

/**
 * ClinicSearch represents the model behind the search form about `common\models\Clinic`.
 */
class ClinicSearch extends Clinic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'score', 'verify_status'], 'integer'],
            [['name', 'address', 'tel', 'chief', 'idcard', 'Business_license_img', 'local_img', 'doctor_certificate_img', 'user_uuid'], 'safe'],
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
        $query = Clinic::find();

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
            'score' => $this->score,
            'verify_status' => $this->verify_status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'chief', $this->chief])
            ->andFilterWhere(['like', 'idcard', $this->idcard])
            ->andFilterWhere(['like', 'Business_license_img', $this->Business_license_img])
            ->andFilterWhere(['like', 'local_img', $this->local_img])
            ->andFilterWhere(['like', 'doctor_certificate_img', $this->doctor_certificate_img])
            ->andFilterWhere(['like', 'user_uuid', $this->user_uuid]);

        $dataProvider->sort->defaultOrder =
            [
                'verify_status'=>SORT_ASC,
                'id'=>SORT_DESC,
            ];
        return $dataProvider;
    }
}
