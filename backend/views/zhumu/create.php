<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Zhumu */

$this->title = 'Create Zhumu';
$this->params['breadcrumbs'][] = ['label' => 'Zhumus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zhumu-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
