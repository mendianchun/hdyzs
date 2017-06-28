<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DrugCode */

$this->title = '新增药品监管码';
$this->params['breadcrumbs'][] = ['label' => '药品监管码管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="drug-code-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
