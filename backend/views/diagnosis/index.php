<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Appointment;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '诊断管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="appointment-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],

	        ['attribute'=>'appointment_no',
		        //'label'=>'诊所名称',
		        'value'=>'appointment_no',
		        'contentOptions'=>['width'=>'80px'],
	        ],
           // 'appointment_no',
//            'clinic_uuid',
            ['attribute'=>'clinicName',
                'label'=>'诊所名称',
                'value'=>'clinicUu.name',
	            'contentOptions'=>['width'=>'80px'],
            ],
//            'expert_uuid',
	        ['attribute'=>'expertName',
		        'label'=>'专家名称',
		        'value'=>'expertUu.name',
		        'contentOptions'=>['width'=>'80px'],
	        ],

	        ['attribute'=>'patient_name',
		        'label'=>'患者名称',
		        'value'=>'patient_name',
		        'contentOptions'=>['width'=>'80px'],
	        ],
           // 'patient_name',
          //  'order_fee',
//            'status',
            ['attribute'=>'dx_status',
                'value'=>'DxStatusStr',
                'filter'=>Appointment::allDxStatus(),
                'contentOptions'=>
            		function($model)
                    {
                        return ($model->dx_status==Appointment::DX_STATUS_DO)?['class'=>'bg-danger','width'=>'80px']:['width'=>'80px'];
                    }
            ],
//            'pay_status',
//            ['attribute'=>'pay_status',
//                'value'=>'PayStatusStr',
//                'filter'=>Appointment::allPayStatus()
//            ],
//            'pay_type',
//            ['attribute'=>'pay_type',
//                'value'=>'PayTypeStatusStr',
//                'filter'=>Appointment::allPayTypeStatus()
//            ],
//            'order_starttime:datetime',
//            'order_endtime:datetime',

            [
                'attribute' => 'order_starttime',
                'format' => ['date', 'php:Y-m-d H:i'],
                'contentOptions'=>['width'=>'120px'],
            ],

            [
                'attribute' => 'order_endtime',
                'format' => ['date', 'php:Y-m-d H:i'],
                'contentOptions'=>['width'=>'120px'],
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
	        //'expert_diagnosis:ntext',


	        [
                'attribute'=>'audio_url',
                'format'=>'html',
		        'value'=>'AudioStatusStr',
//                'content'=>
//                    function($model)
//                    {
//                        if($model->audio_status==Appointment::AUDIO_STATUS_SUCC){
//	                        return $model->audio_url?'<div><audio style="width: 60px" controls=""><source src="'. Url::toRoute(['diagnosis/mp3', 'appointment_no' => $model->appointment_no]).'" type="audio/mp3"></audio></div>':'';
//                        }else{
//                            return '';
//                        }
//
//	                }
	        ],


            // 'pay_type',
            // 'status',
            // 'pay_status',
            // 'is_sms_notify',
            // 'fee_type',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {approve} {pay} {rebuild} {toundx}',
                'buttons' => [
                    'rebuild'=>function($url,$model,$key)
                    {
                        if($model->audio_status != Appointment::AUDIO_STATUS_FAILED){
                            return '';
                        }
                        $options=[
                            'title'=>Yii::t('yii', '重新生成'),
                            'aria-label'=>Yii::t('yii','重新生成'),
                            'data-confirm'=>Yii::t('yii','你确定重新生成音频吗？'),
                            'data-method'=>'post',
                            'data-pjax'=>'0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-retweet"></span>',$url,$options);
                    },

	                'toundx'=>function($url,$model,$key)
	                {
		                if($model->dx_status != Appointment::DX_STATUS_DO){
			                return '';
		                }
		                $options=[
			                'title'=>Yii::t('yii', '修改状态'),
			                'aria-label'=>Yii::t('yii','修改状态'),
			                'data-confirm'=>Yii::t('yii','你确定修改状态为未诊断吗？'),
			                'data-method'=>'post',
			                'data-pjax'=>'0',
		                ];
		                return Html::a('<span class="glyphicon glyphicon-transfer"></span>',$url,$options);
	                },


//                    'approve'=>function($url,$model,$key)
//                    {
//                        if($model->status != Appointment::STATUS_WAITING){
//                            return '';
//                        }
//                        $options=[
//                            'title'=>Yii::t('yii', '审核'),
//                            'aria-label'=>Yii::t('yii','审核'),
//                            'data-confirm'=>Yii::t('yii','你确定通过这次预约吗？'),
//                            'data-method'=>'post',
//                            'data-pjax'=>'0',
//                        ];
//                        return Html::a('<span class="glyphicon glyphicon-check"></span>',$url,$options);
//                    },
//                    'pay'=>function($url,$model,$key)
//                    {
//                        if($model->status != Appointment::STATUS_SUCC || $model->pay_status == Appointment::PAY_STATUS_PAYED){
//                            return '';
//                        }
//                        $options=[
//                            'title'=>Yii::t('yii', '支付'),
//                            'aria-label'=>Yii::t('yii','支付'),
//                            'data-confirm'=>Yii::t('yii','确定已经支付了吗？'),
//                            'data-method'=>'post',
//                            'data-pjax'=>'0',
//                        ];
//                        return Html::a('<span class="glyphicon glyphicon-credit-card"></span>',$url,$options);
//
//                    }

                ]
            ],
        ],
    ]); ?>
</div>
