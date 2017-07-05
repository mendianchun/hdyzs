<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ScoreLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="score-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'clinic_uuid') ?>

    <?= $form->field($model, 'old_score') ?>

    <?= $form->field($model, 'add_score') ?>

    <?= $form->field($model, 'new_score') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
