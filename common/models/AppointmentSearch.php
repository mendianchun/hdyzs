<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Appointment;

/**
 * AppointmentSearch represents the model behind the search form about `common\models\Appointment`.
 */
class AppointmentSearch extends Appointment
{
	public function attributes()
    {
        return array_merge(parent::attributes(),['clinicName','expertName']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appointment_no', 'order_starttime', 'order_endtime', 'order_fee', 'real_starttime', 'real_endtime', 'real_fee', 'patient_age', 'pay_type', 'status', 'pay_status','dx_status', 'is_sms_notify', 'fee_type', 'created_at', 'updated_at'], 'integer'],
            [['clinic_uuid', 'expert_uuid', 'patient_name', 'patient_mobile', 'patient_idcard', 'patient_description', 'expert_diagnosis', 'clinicName', 'expertName'], 'safe'],
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
    public function search($params,$pageSize = 10)
    {
        $query = Appointment::find();

        // add conditions that should always apply here

	    $dataProvider = new ActiveDataProvider([
		    'query' => $query,
		    'pagination' => ['pageSize' => $pageSize],
		    'sort' => [
			    'defaultOrder' => [
				    'order_starttime' => SORT_DESC,
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
            'appointment_no' => $this->appointment_no,
//            'order_starttime' => $this->order_starttime,
//            'order_endtime' => $this->order_endtime,
            'order_fee' => $this->order_fee,
//            'real_starttime' => $this->real_starttime,
//            'real_endtime' => $this->real_endtime,
            'real_fee' => $this->real_fee,
            'patient_age' => $this->patient_age,
            'pay_type' => $this->pay_type,
            'status' => $this->status,
	        'pay_status' => $this->pay_status,
	        'dx_status' => $this->dx_status,
            'is_sms_notify' => $this->is_sms_notify,
            'fee_type' => $this->fee_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'clinic_uuid' => $this->clinic_uuid,
        ]);

        $query->andFilterWhere(['like', 'expert_uuid', $this->expert_uuid])
            ->andFilterWhere(['like', 'patient_name', $this->patient_name])
            ->andFilterWhere(['like', 'patient_mobile', $this->patient_mobile])
            ->andFilterWhere(['like', 'patient_idcard', $this->patient_idcard])
            ->andFilterWhere(['like', 'patient_description', $this->patient_description])
            ->andFilterWhere(['like', 'expert_diagnosis', $this->expert_diagnosis]);

        $query->andFilterWhere(['>=','order_starttime',$this->order_starttime]);
        $query->andFilterWhere(['<','order_starttime',$this->order_endtime]);

        $query->andFilterWhere(['>=','real_starttime',$this->real_starttime]);
        $query->andFilterWhere(['<','real_endtime',$this->real_endtime]);

        $query->join('INNER JOIN','clinic','appointment.clinic_uuid = clinic.user_uuid');
        $query->andFilterWhere(['like','clinic.name',$this->clinicName]);


        $query->join('INNER JOIN','expert','appointment.expert_uuid = expert.user_uuid');
        $query->andFilterWhere(['like','expert.name',$this->expertName]);

        $dataProvider->sort->attributes['clinicName'] =
        [
            'asc'=>['clinic.name'=>SORT_ASC],
            'desc'=>['clinic.name'=>SORT_DESC],
        ];

        $dataProvider->sort->attributes['expertName'] =
        [
            'asc'=>['expert.name'=>SORT_ASC],
            'desc'=>['expert.name'=>SORT_DESC],
        ];

        $dataProvider->sort->defaultOrder =
        [
            'status'=>SORT_ASC,
            'appointment_no'=>SORT_DESC,
        ];

        return $dataProvider;
    }
}
