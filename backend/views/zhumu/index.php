<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Zhumu;


/* @var $this yii\web\View */
/* @var $searchModel common\models\ZhumuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '瞩目管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zhumu-index">
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
            'zcode',
//            'status',
            ['attribute'=>'status',
                'value'=>'StatusStr',
                'filter'=>Zhumu::allStatus()
            ],
            // 'created_at',
            // 'updated_at',
            ['attribute'=>'created_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            ['attribute'=>'updated_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete}'
            ],
        ],
    ]); ?>
</div>
