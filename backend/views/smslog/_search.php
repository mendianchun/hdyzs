<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SmsLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'mobile') ?>

    <?= $form->field($model, 'content') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'user_uuid') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
