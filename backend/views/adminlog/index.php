<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AdminLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '操作日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-log-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'user_id',
            [
                'attribute'=>'username',
                'label'=>'用户名',
                'contentOptions'=>['width'=>'100px'],
                'value' => 'adminuser.username',
            ],
//            'route',
//            'description:ntext',
            [
                'attribute'=>'description',
//                'contentOptions'=>['width'=>'200px'],
                'value' => function ($model) {
                    $pos = strpos($model->description,'的');
                    return substr($model->description,0,$pos);
                },
                'format'=>'ntext',
            ],
//            'created_at',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'contentOptions'=>['width'=>'150px'],
            ],

//             'ip',
            [
                'attribute' => 'ip',
//                'format' => ['date', 'php:Y-m-d H:i:s'],
//                'contentOptions'=>['width'=>'150px'],
                'value' => function ($model) {
                    return long2ip($model->ip);
                },
                'contentOptions'=>['width'=>'100px'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
