<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ScoreLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '积分记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="score-log-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'clinic_uuid',
            ['attribute'=>'clinicName',
                'label'=>'诊所名称',
                'value'=>'clinicUu.name',
            ],
            'old_score',
            'add_score',
            'new_score',
             'reason:ntext',
//             'created_at',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
