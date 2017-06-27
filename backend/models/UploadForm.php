<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/27
 * Time: 下午2:07
 */

namespace backend\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
	/**
	 * @var UploadedFile
	 */
	public $imageFile;

	public function rules()
	{
		return [
			[['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
		];
	}

	public function upload()
	{
		if ($this->validate()) {
			$this->imageFile->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
			$url = 'uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension;
			return $url;
		} else {
			return false;
		}
	}
}