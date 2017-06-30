<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SmsLog */

$this->title = 'Create Sms Log';
$this->params['breadcrumbs'][] = ['label' => 'Sms Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
