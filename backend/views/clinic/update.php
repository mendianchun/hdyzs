<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Clinic */

$this->title = '修改诊所信息: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '诊所管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="clinic-update">
    <?= $this->render('_form', [
        'model' => $model,
	    'op'=>'update',
    ]) ?>

</div>
