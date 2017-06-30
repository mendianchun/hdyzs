<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ExpertTimeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Expert Times';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expert-time-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Expert Time', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'expert_uuid',
            'date',
            'hour',
            'zone',
            // 'is_order',
            // 'clinic_uuid',
            // 'order_no',
            // 'status',
            // 'reason',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
