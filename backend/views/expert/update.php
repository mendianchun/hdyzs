<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Expert */

$this->title = '更新专家: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '专家管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="expert-update">
    <?= $this->render('_form', [
        'model' => $model,
	    'time_conf' => $time_conf,
        'op'=>'update',
    ]) ?>

</div>
