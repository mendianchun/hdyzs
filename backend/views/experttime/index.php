<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ExpertTime;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ExpertTimeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约时段管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expert-time-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
	        ['attribute'=>'expertName',
		        'label'=>'专家名称',
		        'value'=>'expert.name',
	        ],
	        ['attribute'=>'expertName',
		        'label'=>'患者名称',
		        'value'=>'appointment.patient_name',
	        ],
	        //'expert.name',
	        //'appointment.patient_name',
            //'expert_uuid',
            'date',
            'hour',
	        ['attribute'=>'zone',
		        'value'=>'ZoneStr',
		        'filter'=>ExpertTime::allZone()
	        ],

	        ['attribute'=>'order_status',
		        'label'=>'预约状态',
		        'value'=>'OrderStatus',
		        'filter'=>ExpertTime::allOrderStatus()
	        ],
	        ['attribute'=>'status',
		        'value'=>'StatusStr',
		        'filter'=>ExpertTime::allStatus()
	        ],
           // 'status',
            //'is_order',
            // 'clinic_uuid',
            // 'order_no',
           //  'expertName',
            // 'reason',

	        ['class' => 'yii\grid\ActionColumn',
		        'template' => '{view} {delete} ',
		        'buttons' => [
			        'delete'=>function($url,$model,$key)
			        {
				        if($model->order_no >0){
					        return '';
				        }
				        $options=[
					        'title'=>Yii::t('yii', '删除'),
					        'aria-label'=>Yii::t('yii','删除'),
					        'data-confirm'=>Yii::t('yii','你确定删除这个时段吗？'),
					        'data-method'=>'post',
					        'data-pjax'=>'0',
				        ];
				        return Html::a('<span class="glyphicon glyphicon-check"></span>',$url,$options);
			        },

		        ]
	        ],
        ],
    ]); ?>
</div>
