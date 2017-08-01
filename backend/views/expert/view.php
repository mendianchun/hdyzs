<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Expert */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Experts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expert-view">
    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
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
            'name',
            array(
	            'label' => '照片',
	            'value' =>Yii::$app->params['domain'].$model->head_img,
	            'format' => ['image',['width'=>'30','height'=>'30',]],
            ),
            //'head_img',
            'free_time:ntext',
            'fee_per_times',
            'fee_per_hour',
            'skill',
	        'introduction:ntext',
	        'url:ntext',
           // 'user_uuid',
        ],
    ]) ?>

</div>
