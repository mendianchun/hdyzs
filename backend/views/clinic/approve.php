<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use common\models\Clinic;

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
            'Business_license_img',
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
//            'user_uuid',
        ],
    ]) ?>

</div>

<div class="clinic-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'verify_reason')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('通过', ['class' => 'btn btn-primary','name'=>'submitButton','value'=>Clinic::STATUS_SUCC]) ?>
        <?= Html::submitButton('不通过', ['class' => 'btn btn-danger','name'=>'submitButton','value'=>Clinic::STATUS_FAILED]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
