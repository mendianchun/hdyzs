<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_no')->textInput() ?>

    <?= $form->field($model, 'clinic_uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'expert_uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_starttime')->textInput() ?>

    <?= $form->field($model, 'order_endtime')->textInput() ?>

    <?= $form->field($model, 'order_fee')->textInput() ?>

    <?= $form->field($model, 'real_starttime')->textInput() ?>

    <?= $form->field($model, 'real_endtime')->textInput() ?>

    <?= $form->field($model, 'real_fee')->textInput() ?>

    <?= $form->field($model, 'patient_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'patient_age')->textInput() ?>

    <?= $form->field($model, 'patient_mobile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'patient_idcard')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'patient_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'expert_diagnosis')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pay_type')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'pay_status')->textInput() ?>

    <?= $form->field($model, 'is_sms_notify')->textInput() ?>

    <?= $form->field($model, 'fee_type')->textInput() ?>

    <?= $form->field($model, 'create_at')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
