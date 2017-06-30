<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ExpertTime */

$this->title = 'Update Expert Time: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Expert Times', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="expert-time-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
