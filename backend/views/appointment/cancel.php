<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/30
 * Time: 下午4:44
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Appointment */

$this->title = 'Update Appointment: ' . $model->appointment_no;
$this->params['breadcrumbs'][] = ['label' => 'Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->appointment_no, 'url' => ['view', 'id' => $model->appointment_no]];
$this->params['breadcrumbs'][] = '取消';
?>
<div class="appointment-update">

	<div class="appointment-form">

		<?php $form = ActiveForm::begin(); ?>

		<?= $form->field($model, 'appointment_no')->textInput(['maxlength' => 20, 'readonly' => 'true'])  ?>

		<?= $form->field($model, 'cancel_reason')->textInput() ?>

		<div class="form-group">
			<?= Html::submitButton('取消', ['class' => 'btn btn-primary']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	</div>

</div>