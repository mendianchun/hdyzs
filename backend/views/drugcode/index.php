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
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'code',
//            'create_at',
            [
                'attribute' => 'create_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],
//            'clinic_uuid',
            ['attribute'=>'clinicName',
                'label'=>'诊所名称',
                'value'=>'clinicUu.name',
            ],
//            'submit_at',
            [
                'attribute' => 'submit_at',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
        ],
    ]); ?>
</div>
