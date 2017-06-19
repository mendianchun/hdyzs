<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ZhumuStatus;


/* @var $this yii\web\View */
/* @var $searchModel common\models\ZhumuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '瞩目管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zhumu-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建瞩目账户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            ['attribute'=>'id',
//                'contentOptions'=>['width'=>'30px'],
//            ],
//            'uuid',
            'username',
            'password',
//            'status',
            ['attribute'=>'status',
                'value'=>'status0.name',
                'filter'=>ZhumuStatus::find()
                    ->select(['name','status'])
                    ->orderBy('id')
                    ->indexBy('status')
                    ->column(),
            ],
            // 'create_at',
            // 'update_at',
            ['attribute'=>'create_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            ['attribute'=>'update_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{update}{delete}'
            ],
        ],
    ]); ?>
</div>
