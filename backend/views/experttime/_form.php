<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ExpertTime */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expert-time-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'expert_uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hour')->textInput() ?>

    <?= $form->field($model, 'zone')->textInput() ?>

    <?= $form->field($model, 'is_order')->textInput() ?>

    <?= $form->field($model, 'clinic_uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_no')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
