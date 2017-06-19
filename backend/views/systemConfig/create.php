<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SystemConfig */

$this->title = '新增系统配置';
$this->params['breadcrumbs'][] = ['label' => '系统配置管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
