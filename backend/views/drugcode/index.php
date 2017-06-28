<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DrugCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '药品监管码管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="drug-code-index">

    <p>
        <?= Html::a('新增药品监管码', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('导入药品监管码', ['import'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('诊所提交的监管码', ['drugcodeclinic/index'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'code',
//            'created_at',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
        ],
    ]); ?>
</div>
