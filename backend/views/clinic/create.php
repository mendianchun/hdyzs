<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Clinic */

$this->title = 'Create Clinic';
$this->params['breadcrumbs'][] = ['label' => 'Clinics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clinic-create">
    <?= $this->render('_form', [
        'model' => $model,
	    'op'=>'create',
    ]) ?>

</div>
