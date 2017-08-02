<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SmsLog;

/**
 * SmsLogSearch represents the model behind the search form about `common\models\SmsLog`.
 */
class SmsLogSearch extends SmsLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'status'], 'integer'],
            [['mobile', 'content'], 'safe'],
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
        $query = SmsLog::find();

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
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'mobile', $this->mobile])
            ->andFilterWhere(['like', 'content', $this->content]);

        if($this->status == self::STATUS_SUCC){
            $query->andFilterWhere(['=', 'status', $this->status]);
        }else{
            //大于1都是失败
            $query->andFilterWhere(['>=', 'status', $this->status]);
        }
	    $dataProvider->sort->defaultOrder =
		    [
			    'created_at'=>SORT_DESC,
		    ];

        return $dataProvider;
    }
}
