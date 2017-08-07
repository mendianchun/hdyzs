<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ExpertTime */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '预约时段管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expert-time-view">

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定要删除吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'expert_uuid',
            'date',
            'hour',
            'zone',
            'is_order',
            'clinic_uuid',
            'order_no',
            'status',
            'reason',
        ],
    ]) ?>

</div>
