<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DrugCodeClinic */

$this->title = 'Update Drug Code Clinic: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Drug Code Clinics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="drug-code-clinic-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
