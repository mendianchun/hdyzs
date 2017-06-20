<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Zhumu */

$this->title = '更新瞩目账户: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => '瞩目管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="zhumu-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
