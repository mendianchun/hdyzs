<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\SmsLog;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SmsLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '短信日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-log-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
                'headerOptions'=>['width'=>'20px'],
//                'contentOptions'=>['width'=>'20px'],
            ],
//            'mobile',
            [
                'attribute' => 'mobile',
                'headerOptions'=>['width'=>'100px'],
//                'contentOptions'=>['width'=>'100px'],
            ],
            'content',
//            'created_at',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'headerOptions'=>['width'=>'150px'],
//                'contentOptions'=>['width'=>'150px'],
            ],
//            'status',
            ['attribute'=>'status',
                'value'=>'StatusStr',
                'filter'=>SmsLog::allStatus(),
                'headerOptions'=>['width'=>'50px'],
//                'contentOptions'=>['width'=>'50px'],
            ],

//            ['class' => 'yii\grid\ActionColumn',
//                'template' => '{view}',
//            ],
        ],
    ]); ?>
</div>
