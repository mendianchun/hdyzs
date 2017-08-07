<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SystemConfig */

$this->title = '更新系统配置: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '系统配置管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="system-config-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
