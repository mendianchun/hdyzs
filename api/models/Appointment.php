<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/14
 * Time: 下午2:46
 */

namespace api\models;
use Yii;

class Appointment extends \common\models\Appointment
{

	public function fields()
	{
		$fields = parent::fields();

		// 去掉一些包含敏感信息的字段
		unset($fields['patient_mobile'], $fields['patient_idcard'], $fields['create_at']);

		return $fields;
	}

}