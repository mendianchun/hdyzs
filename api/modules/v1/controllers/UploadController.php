<?php
namespace api\modules\v1\controllers;

use Yii;

use api\modules\ApiBaseController;
use yii\helpers\ArrayHelper;
use common\service\Service;

//use common\models\UploadForm;
//use yii\web\UploadedFile;

use yii\web\HttpException;

class UploadController extends ApiBaseController{


    public $modelClass = 'common\models\UploadForm';//对应的数据模型处理控制器


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
//                    'img'
                ],
            ]
        ]);
    }

    /**
     * ajax上传图片
     */
    public function actionImg($type)
    {
        if(!in_array($type,Yii::$app->params['upload.type'])){
            return Service::sendError(20801,'类型错误');
        }

        $targetFolder = \Yii::$app->basePath.'/web/uploads/'.$type.'/'.date('Y/md');
        $file = new \yii\helpers\FileHelper();
        $file->createDirectory($targetFolder);
//        return Yii::$app->request->post();
//        return $_FILES;
        if (!empty($_FILES)) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $fileSize = $_FILES['Filedata']['size'];
            $fileParts = pathinfo($_FILES['Filedata']['name']);
            $extension = $fileParts['extension'];
            $random = time() . rand(1000, 9999);
            $randName = $random . "." . $extension;
            $targetFile = rtrim($targetFolder,'/') . '/' . $randName;
            $uploadfile_path = Yii::$app->request->hostInfo.'/uploads/'.$type.'/'.date('Y/md').'/'.$randName;
            $callback['url'] = $uploadfile_path;
//            $callback['filename'] = $fileParts['filename'];
//            $callback['randName'] = $random;

            //检查文件类型
            if (!in_array($fileParts['extension'],Yii::$app->params['upload.imageType'])) {
                return Service::sendError(20802,'不能上传后缀为'.$fileParts['extension'].'文件');
            }

            //检查文件大小
            if($fileSize > Yii::$app->params['upload.maxsize'] * 1024* 1024){
                return Service::sendError(20803,'文件大小不能超过'.Yii::$app->params['upload.maxsize'].'MB');
            }
            move_uploaded_file($tempFile,$targetFile);
            return Service::sendSucc($callback);
        }else{
            return Service::sendError(20804,'没有上传文件');
        }

//        $model = new UploadForm();
//
//        $data = array();
//        if (Yii::$app->request->isPost) {
//            $model->file = UploadedFile::getInstance($model, 'file');
//
//            if ($model->file && $model->validate()) {
//                $model->file->saveAs('uploads/' . $model->file->baseName . '.' . $model->file->extension);
//                $code = 0;
//                $message = 'succ';
//                $data['url'] = 'uploads/' . $model->file->baseName . '.' . $model->file->extension;
//            } else {
//                $code = 600;
//                $message = array_values($model->getFirstErrors())[0];
//                $data = [];
//            }
//        }else {
//            $code = 600;
//            $message = 'no data';
//            $data = [];
//        }
//
//        return [
//            'code' => $code,
//            'data' => $data,
//            'message' => $message,
//        ];
    }
}