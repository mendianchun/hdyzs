<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Zhumu;

/* @var $this yii\web\View */
/* @var $model common\models\Zhumu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="zhumu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(Zhumu::allStatus()); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
