<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ScoreLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="score-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'clinic_uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'old_score')->textInput() ?>

    <?= $form->field($model, 'add_score')->textInput() ?>

    <?= $form->field($model, 'new_score')->textInput() ?>

    <?= $form->field($model, 'reason')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
