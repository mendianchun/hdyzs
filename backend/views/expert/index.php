<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ExpertSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '专家管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expert-index">
    <p>
        <?= Html::a('添加专家', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
         //   ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
	        ['attribute'=>'username',
		        'label'=>'专家ID',
		        'value'=>'userUu.username',
	        ],
            'head_img',
          //  'free_time:ntext',
            'fee_per_times',
            // 'fee_per_hour',
            // 'skill',
            // 'introduction:ntext',
            // 'user_uuid',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
