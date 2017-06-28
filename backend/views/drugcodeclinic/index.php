<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DrugCodeClinicSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '诊所提交的监管码';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="drug-code-clinic-index">

    <?= Html::a('返回', ['drugcode/index'], ['class' => 'btn btn-success']) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'code',
            //            'clinic_uuid',
            ['attribute'=>'clinicName',
                'label'=>'诊所名称',
                'value'=>'clinicUu.name',
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
