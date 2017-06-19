<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SystemConfig */

$this->title = '更新系统配置: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '系统配置管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="system-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
