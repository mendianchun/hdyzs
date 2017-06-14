<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Pay */

$this->title = 'Update Pay: ' . $model->pay_no;
$this->params['breadcrumbs'][] = ['label' => 'Pays', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pay_no, 'url' => ['view', 'id' => $model->pay_no]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pay-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
