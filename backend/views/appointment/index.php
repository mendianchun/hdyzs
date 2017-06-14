<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Appointments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appointment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Appointment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'appointment_no',
            'clinic_uuid',
            'expert_uuid',
            'order_starttime:datetime',
            'order_endtime:datetime',
            // 'order_fee',
            // 'real_starttime:datetime',
            // 'real_endtime:datetime',
            // 'real_fee',
            // 'patient_name',
            // 'patient_age',
            // 'patient_mobile',
            // 'patient_idcard',
            // 'patient_description:ntext',
            // 'expert_diagnosis:ntext',
            // 'pay_type',
            // 'status',
            // 'pay_status',
            // 'is_sms_notify',
            // 'fee_type',
            // 'create_at',
            // 'update_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
