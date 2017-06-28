<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ExpertSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Experts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expert-index">
    <p>
        <?= Html::a('Create Expert', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'head_img',
          //  'free_time:ntext',
            'fee_per_times:datetime',
            // 'fee_per_hour',
            // 'skill',
            // 'introduction:ntext',
            // 'user_uuid',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
