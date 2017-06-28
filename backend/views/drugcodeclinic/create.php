<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DrugCodeClinic */

$this->title = 'Create Drug Code Clinic';
$this->params['breadcrumbs'][] = ['label' => 'Drug Code Clinics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="drug-code-clinic-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
