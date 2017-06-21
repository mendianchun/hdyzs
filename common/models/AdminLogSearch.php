<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AdminLog;

/**
 * AdminLogSearch represents the model behind the search form about `common\models\AdminLog`.
 */
class AdminLogSearch extends AdminLog
{
    public function attributes()
    {
        return array_merge(parent::attributes(), ['username']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'create_at', 'user_id', 'ip'], 'integer'],
            [['route', 'description', 'username'], 'safe'],
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
        $query = AdminLog::find();

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
            'create_at' => $this->create_at,
            'user_id' => $this->user_id,
            'ip' => $this->ip,
        ]);

        $query->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'description', $this->description]);

        $query->join('INNER JOIN','adminuser','admin_log.user_id = adminuser.id');
        $query->andFilterWhere(['like','adminuser.username',$this->username]);

        $dataProvider->sort->attributes['username'] =
            [
                'asc'=>['adminuser.username'=>SORT_ASC],
                'desc'=>['adminuser.username'=>SORT_DESC],
            ];
        return $dataProvider;
    }
}
