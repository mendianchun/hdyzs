<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Appointment */

$this->title = $model->appointment_no;
$this->params['breadcrumbs'][] = ['label' => 'Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appointment-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'appointment_no',
//            'clinic_uuid',
            ['attribute'=>'clinicName',
                'label'=>'诊所名称',
                'value'=>$model->clinicUu->name,
            ],
//            'expert_uuid',
            ['attribute'=>'expertName',
                'label'=>'专家名称',
                'value'=>$model->expertUu->name,
            ],
//            'order_starttime:datetime',
            [
                'attribute'=>'order_starttime',
                'format'=>['date','php:Y-m-d H:i:s']
            ],
//            'order_endtime:datetime',
            [
                'attribute'=>'order_endtime',
                'format'=>['date','php:Y-m-d H:i:s']
            ],
            'order_fee',
//            'real_starttime:datetime',
            [
                'attribute'=>'real_starttime',
                'format'=>['date','php:Y-m-d H:i:s']
            ],
//            'real_endtime:datetime',
            [
                'attribute'=>'real_endtime',
                'format'=>['date','php:Y-m-d H:i:s']
            ],
            'real_fee',
            'patient_name',
            'patient_age',
            'patient_mobile',
            'patient_idcard',
            'patient_description:ntext',
            'expert_diagnosis:ntext',
            //            'status',
            ['attribute'=>'status',
                'value'=>$model->StatusStr,
            ],
//            'pay_type',
            ['attribute'=>'pay_type',
                'value'=>$model->PayTypeStatusStr,
            ],
//            'pay_status',
            ['attribute'=>'pay_status',
                'value'=>$model->PayStatusStr,
            ],
//            'is_sms_notify',
//            'fee_type',
            ['attribute'=>'fee_type',
                'value'=>$model->FeeTypeStatusStr,
            ],
//            'create_at',
            [
                'attribute'=>'create_at',
                'format'=>['date','php:Y-m-d H:i:s']
            ],
//            'update_at',
            [
                'attribute'=>'update_at',
                'format'=>['date','php:Y-m-d H:i:s']
            ],
        ],
    ]) ?>

</div>
