<?php
/**
 * Created by PhpStorm.
 * User: damen
 * Date: 2017/6/5
 * Time: 下午3:53
 */

namespace common\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class DrugCodeUploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'txt', 'maxSize' => 1024*1024*Yii::$app->params['upload.maxsize'],],
        ];
    }
}