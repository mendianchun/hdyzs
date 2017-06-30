<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ExpertTimeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expert-time-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'expert_uuid') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'hour') ?>

    <?= $form->field($model, 'zone') ?>

    <?php // echo $form->field($model, 'is_order') ?>

    <?php // echo $form->field($model, 'clinic_uuid') ?>

    <?php // echo $form->field($model, 'order_no') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
