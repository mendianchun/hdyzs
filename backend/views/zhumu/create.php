<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Zhumu */

$this->title = '创建瞩目账户';
$this->params['breadcrumbs'][] = ['label' => '瞩目管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zhumu-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
