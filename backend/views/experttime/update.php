<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ExpertTime */

$this->title = '更新预约时段: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '预约时段管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="expert-time-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
