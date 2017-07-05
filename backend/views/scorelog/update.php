<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ScoreLog */

$this->title = 'Update Score Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Score Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="score-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
