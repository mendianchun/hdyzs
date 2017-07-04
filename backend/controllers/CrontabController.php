<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/7/4
 * Time: 上午10:57
 */


namespace console\controllers;

use yii\console\Controller;
use common\models\ExpertTime;
use common\models\Expert;

class CrontabController extends Controller
{
	public function actionCreatfreetime(){
		$experts = Expert::findAll();
		echo '<pre>';
		var_dump($experts);
		exit();


	}



}