<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ExpertTime */

$this->title = 'Create Expert Time';
$this->params['breadcrumbs'][] = ['label' => 'Expert Times', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expert-time-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
