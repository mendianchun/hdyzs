<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Expert */

$this->title = '创建专家';
$this->params['breadcrumbs'][] = ['label' => '专家管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expert-create">
    <?= $this->render('_form', [
	    'model' => $model,
	    'time_conf' => $time_conf,
	    'op'=>'create',
    ]) ?>

</div>
