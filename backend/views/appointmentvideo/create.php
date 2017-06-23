<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AppointmentVideo */

$this->title = 'Create Appointment Video';
$this->params['breadcrumbs'][] = ['label' => 'Appointment Videos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appointment-video-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
