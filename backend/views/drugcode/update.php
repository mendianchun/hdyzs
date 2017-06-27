<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DrugCode */

$this->title = 'Update Drug Code: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Drug Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="drug-code-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
