<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Appointment;

use yii\bootstrap\Modal;
// 更新操作
Modal::begin([
	'id' => 'cancel-modal',
	'header' => '<h4 class="modal-title">更新</h4>',
	'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]);
Modal::end();

$requestUpdateUrl = Url::toRoute('cancel');
$updateJs = <<<JS
    $('.data-cancel').on('click', function () {
        $.get('{$requestUpdateUrl}', { id: $(this).closest('tr').data('key') },
            function (data) {
                $('.modal-body').html(data);
            }  
        );
    });
JS;
$this->registerJs($updateJs);

/* @var $this yii\web\View */
/* @var $searchModel common\models\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '预约管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appointment-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'appointment_no',
//            'clinic_uuid',
            ['attribute'=>'clinicName',
                'label'=>'诊所名称',
                'value'=>'clinicUu.name',
            ],
//            'expert_uuid',
            ['attribute'=>'expertName',
                'label'=>'专家名称',
                'value'=>'expertUu.name',
            ],
            'patient_name',
//            'order_fee',
//            'status',
            ['attribute'=>'status',
                'value'=>'StatusStr',
                'filter'=>Appointment::allStatus(),
                'contentOptions'=>
            		function($model)
                    {
                        return ($model->status==Appointment::STATUS_WAITING)?['class'=>'bg-danger']:[];
                    }
            ],
//            'pay_status',
            ['attribute'=>'pay_status',
                'value'=>'PayStatusStr',
                'filter'=>Appointment::allPayStatus()
            ],
//            'pay_type',
            ['attribute'=>'pay_type',
                'value'=>'PayTypeStatusStr',
                'filter'=>Appointment::allPayTypeStatus()
            ],
//            'order_starttime:datetime',
//            'order_endtime:datetime',

            [
                'attribute' => 'order_starttime',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'contentOptions'=>['width'=>'150px'],
            ],

            [
                'attribute' => 'order_endtime',
                'format' => ['date', 'php:Y-m-d H:i:s'],
                'contentOptions'=>['width'=>'150px'],
            ],
            // 'order_fee',
            // 'real_starttime:datetime',
            // 'real_endtime:datetime',
            // 'real_fee',
            // 'patient_name',
            // 'patient_age',
            // 'patient_mobile',
            // 'patient_idcard',
            // 'patient_description:ntext',
            // 'expert_diagnosis:ntext',
            // 'pay_type',
            // 'status',
            // 'pay_status',
            // 'is_sms_notify',
            // 'fee_type',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {approve} {pay}{cancel}',
                'buttons' => [
                    'approve'=>function($url,$model,$key)
                    {
                        if($model->status != Appointment::STATUS_WAITING){
                            return '';
                        }
                        $options=[
                            'title'=>Yii::t('yii', '审核'),
                            'aria-label'=>Yii::t('yii','审核'),
                            'data-confirm'=>Yii::t('yii','你确定通过这次预约吗？'),
                            'data-method'=>'post',
                            'data-pjax'=>'0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-check"></span>',$url,$options);
                    },
                    'pay'=>function($url,$model,$key)
                    {
                        if($model->status != Appointment::STATUS_SUCC || $model->pay_status == Appointment::PAY_STATUS_PAYED){
                            return '';
                        }
                        $options=[
                            'title'=>Yii::t('yii', '支付'),
                            'aria-label'=>Yii::t('yii','支付'),
                            'data-confirm'=>Yii::t('yii','确定已经支付了吗？'),
                            'data-method'=>'post',
                            'data-pjax'=>'0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-credit-card"></span>',$url,$options);

                    },
                    'cancel' => function ($url, $model, $key) {

	                    if($model->status == Appointment::STATUS_CANCLE){
		                    return '';
	                    }
		                return Html::a('取消', '#', [
			                'data-toggle' => 'modal',
			                'data-target' => '#cancel-modal',
			                'class' => 'data-cancel',
			                'data-id' => $key,
		                ]);
	                },

                ]
            ],
        ],
    ]); ?>
</div>
