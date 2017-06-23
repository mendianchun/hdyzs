<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppointmentVideo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="appointment-video-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'appointment_no')->textInput() ?>

    <?= $form->field($model, 'zhumu_uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'meeting_number')->textInput() ?>

    <?= $form->field($model, 'audio_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'create_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
