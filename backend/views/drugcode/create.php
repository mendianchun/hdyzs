<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DrugCode */

$this->title = 'Create Drug Code';
$this->params['breadcrumbs'][] = ['label' => 'Drug Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="drug-code-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
