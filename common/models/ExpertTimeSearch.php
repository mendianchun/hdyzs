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
	public $order_status;

	public function attributes()
	{
		return array_merge(parent::attributes(),['patientName','expertName']);
	}
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hour', 'zone', 'is_order', 'status'], 'integer'],
            [['expert_uuid', 'date', 'clinic_uuid', 'order_no', 'reason','patientName', 'expertName','order_status'], 'safe'],
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
    public function search($params, $pageSize = 10)
    {
        $query = ExpertTime::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
	        'query' => $query,
	        'pagination' => ['pageSize' => $pageSize],
	        'sort' => [
		        'defaultOrder' => [
			        'id' => SORT_DESC,
		        ],
		        //'attributes'=>['id','title'],
	        ],
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
            'expert_time.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'expert_uuid', $this->expert_uuid])
            ->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'clinic_uuid', $this->clinic_uuid])
            ->andFilterWhere(['like', 'order_no', $this->order_no])
            ->andFilterWhere(['like', 'reason', $this->reason]);

	    $query->join('left JOIN','appointment','expert_time.order_no = appointment.appointment_no');
	    $query->andFilterWhere(['like','appointment.patient_name',$this->patientName]);

	    if(isset($params['ExpertTimeSearch']['order_status'])){
		    if($params['ExpertTimeSearch']['order_status']==1){
			    $query->andWhere(['!=','order_no',0]);
		    }elseif($params['ExpertTimeSearch']['order_status']==2){
			    $query->andWhere(['=','order_no',0]);
		    }
	    }

	    $query->join('left JOIN','expert','expert_time.expert_uuid = expert.user_uuid');
	    $query->andFilterWhere(['like','expert.name',$this->expertName]);

	    $query->andFilterWhere(['>', 'date', date("Y-m-d",strtotime("-1 day") )]);

	    $dataProvider->sort->attributes['patientName'] =
		    [
			    'asc'=>['appointment.patient_name'=>SORT_ASC],
			    'desc'=>['appointment.patient_name'=>SORT_DESC],
		    ];

	    $dataProvider->sort->attributes['expertName'] =
		    [
			    'asc'=>['expert.name'=>SORT_ASC],
			    'desc'=>['expert.name'=>SORT_DESC],
		    ];

	  //	    $posts = $dataProvider->getModels();
//	echo    $query->createCommand()->getRawSql();
//	    echo '<pre>';
//	    var_dump($query);
//exit();
        return $dataProvider;
    }
}
