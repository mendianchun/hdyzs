<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->order_no;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->order_no], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->order_no], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'order_no',
            'clinic_uuid',
            'expert_uuid',
            'order_starttime:datetime',
            'order_endtime:datetime',
            'order_fee',
            'real_starttime:datetime',
            'real_endtime:datetime',
            'real_fee',
            'patient_name',
            'patient_age',
            'patient_mobile',
            'patient_idcard',
            'patient_description:ntext',
            'expert_diagnosis:ntext',
            'pay_type',
            'status',
            'pay_status',
            'is_sms_notify',
            'fee_type',
            'create_at',
            'update_at',
        ],
    ]) ?>

</div>
