<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Clinic;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ClinicSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '诊所管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clinic-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
	        ['attribute'=>'username',
		        'label'=>'诊所ID',
		        'value'=>'userUu.username',
	        ],
            'address',
            'tel',
            'chief',
             'idcard',
            // 'Business_license_img',
            // 'local_img',
            // 'doctor_certificate_img',
             'score',
//             'verify_status',
            ['attribute'=>'verify_status',
                'value'=>'StatusStr',
                'filter'=>Clinic::allStatus(),
                'contentOptions'=>
                    function($model)
                    {
                        return ($model->verify_status==Clinic::STATUS_WAITING)?['class'=>'bg-danger']:[];
                    }
            ],
            // 'user_uuid',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {approve} {pay}',
                'buttons' => [
                    'approve'=>function($url,$model,$key)
                    {
                        if($model->verify_status != Clinic::STATUS_WAITING){
                            return '';
                        }

                        return Html::a('<span class="glyphicon glyphicon-check"></span>',$url);
                    },
                ]
            ],
        ],
    ]); ?>
</div>
