<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ExpertSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expert-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'head_img') ?>

    <?= $form->field($model, 'free_time') ?>

    <?= $form->field($model, 'fee_per_times') ?>

    <?php // echo $form->field($model, 'fee_per_hour') ?>

    <?php // echo $form->field($model, 'skill') ?>

    <?php // echo $form->field($model, 'introduction') ?>

    <?php // echo $form->field($model, 'user_uuid') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
