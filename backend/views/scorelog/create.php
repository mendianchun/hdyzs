<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ScoreLog */

$this->title = 'Create Score Log';
$this->params['breadcrumbs'][] = ['label' => 'Score Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="score-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
