<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Expert */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expert-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?php if($op=='create'){?>
	<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
    <?php }?>
	<?= $form->field($model, 'head_img')->widget('manks\FileInput', [])?>



    <?= $form->field($model,'free_time')->checkboxList($time_conf)?>



    <?= $form->field($model, 'fee_per_times')->textInput() ?>

    <?= $form->field($model, 'fee_per_hour')->textInput() ?>

    <?= $form->field($model, 'skill')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'introduction')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
