<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SystemConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统配置管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-config-index">
    <p>
        <?= Html::a('新增系统配置', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            'value:ntext',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
