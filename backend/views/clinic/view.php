<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Clinic */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Clinics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clinic-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'address',
            'tel',
            'chief',
            'idcard',
//            'Business_license_img',
            [
                'attribute'=>'Business_license_img',
                'format'=>'raw',
                'value'=> Html::img($model->Business_license_img, ['width' => '300px']),
            ],
//            'local_img',
            [
                'attribute'=>'local_img',
                'format'=>'raw',
                'value'=> Html::img($model->Business_license_img, ['width' => '300px']),
            ],
//            'doctor_certificate_img',
            [
                'attribute'=>'doctor_certificate_img',
                'format'=>'raw',
                'value'=> Html::img($model->Business_license_img, ['width' => '300px']),
            ],
            'score',
//            'verify_status',
            [
                'attribute'=>'verify_status',
                'value' => $model->StatusStr,
            ],
            'verify_reason:ntext',
//            'user_uuid',
        ],
    ]) ?>

</div>
