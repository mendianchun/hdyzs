<?php
/**
 * Created by PhpStorm.
 * User: damen
 * Date: 2017/6/27
 * Time: 下午2:42
 */

use yii\widgets\ActiveForm;
$this->title = '导入药品监管码';
$this->params['breadcrumbs'][] = ['label' => '药品监管码管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<?= $form->field($model, 'file')->fileInput() ?>
<div class="help-block">*只允许导入txt类型的文件，文件大小小于<?=Yii::$app->params['upload.maxsize']?>M</div>
    <button>导入</button>

<?php ActiveForm::end() ?>

<?php

    if($totalCnt > 0){
        echo "导入完成<br>";
        echo "总数：".$totalCnt."<br>";
        echo "成功数：".$okCnt."<br>";
        echo "格式错误数：".$dataErrorCnt."<br>";
        echo "失败数：".$failedCnt."<br>";
    }

?>
