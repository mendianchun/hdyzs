<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Expert */

$this->title = 'Update Expert: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Experts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="expert-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
