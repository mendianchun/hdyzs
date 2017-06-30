<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SmsLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'mobile',
            'content',
            'created_at',
            'status',
            'user_uuid',
        ],
    ]) ?>

</div>
