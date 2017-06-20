<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ClinicSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clinics';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clinic-index">
    <p>
        <?= Html::a('Create Clinic', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'address',
            'tel',
            'chief',
            // 'idcard',
            // 'Business_license_img',
            // 'local_img',
            // 'doctor_certificate_img',
            // 'score',
            // 'verify_status',
            // 'user_uuid',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
