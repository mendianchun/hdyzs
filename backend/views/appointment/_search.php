<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppointmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="appointment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'appointment_no') ?>

    <?= $form->field($model, 'clinic_uuid') ?>

    <?= $form->field($model, 'expert_uuid') ?>

    <?= $form->field($model, 'order_starttime') ?>

    <?= $form->field($model, 'order_endtime') ?>

    <?php // echo $form->field($model, 'order_fee') ?>

    <?php // echo $form->field($model, 'real_starttime') ?>

    <?php // echo $form->field($model, 'real_endtime') ?>

    <?php // echo $form->field($model, 'real_fee') ?>

    <?php // echo $form->field($model, 'patient_name') ?>

    <?php // echo $form->field($model, 'patient_age') ?>

    <?php // echo $form->field($model, 'patient_mobile') ?>

    <?php // echo $form->field($model, 'patient_idcard') ?>

    <?php // echo $form->field($model, 'patient_description') ?>

    <?php // echo $form->field($model, 'expert_diagnosis') ?>

    <?php // echo $form->field($model, 'pay_type') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'pay_status') ?>

    <?php // echo $form->field($model, 'is_sms_notify') ?>

    <?php // echo $form->field($model, 'fee_type') ?>

    <?php // echo $form->field($model, 'create_at') ?>

    <?php // echo $form->field($model, 'update_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
