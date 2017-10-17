<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/27
 * Time: 下午4:05
 */

namespace common\components;


use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\base\Exception;
use yii\helpers\FileHelper;
use common\service\Service;


use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use yii\helpers\Html;
use yii\imagine\Image;

/**
 * 文件上传处理
 */
class Upload extends Model
{
	const THUMBNAIL_OUTBOUND = ManipulatorInterface::THUMBNAIL_OUTBOUND;
	const THUMBNAIL_INSET = ManipulatorInterface::THUMBNAIL_INSET;
	const QUALITY = 50;
	const MKDIR_MODE = 0755;
	public static $cacheExpire = 0;

	public $file;


	private $_appendRules;


	public function init ()
	{
		parent::init();
		$extensions = Yii::$app->params['webuploader']['baseConfig']['accept']['extensions'];
		$this->_appendRules = [
			[['file'], 'file', 'extensions' => $extensions],
		];
	}


	public function rules()
	{
		$baseRules = [];
		return array_merge($baseRules, $this->_appendRules);
	}


	/**
	 *
	 */
	public function upImage ()
	{
		$model = new static;
		$model->file = UploadedFile::getInstanceByName('file');
		if (!$model->file) {
			return false;
		}


		$relativePath = $successPath = '';


		if ($model->validate()) {
			$relativePath = Yii::$app->params['imageUploadRelativePath'].date('Ymd').'/';
			$successPath = Yii::$app->params['imageUploadSuccessPath'].date('Ymd').'/';
			$img_id = date('Ymd').substr(Service::create_uuid(),0,8);
			//$fileName = $model->file->baseName . '.' . $model->file->extension;
			$fileName = $img_id . '.' . $model->file->extension;


			if (!is_dir($relativePath)) {
				FileHelper::createDirectory($relativePath);
			}
			$model->file->saveAs($relativePath . $fileName);

			self::thumbnailImg($relativePath ,$fileName ,200,200);



			return [
				'code' => 0,
				'url' => Yii::$app->params['domain'] . $successPath . $fileName,
				'attachment' => $successPath . $fileName
			];


		} else {
			$errors = $model->errors;
			return [
				'code' => 1,
				'msg' => current($errors)[0]
			];
		}
	}


	public function thumb($relativePath ,$fileName ){
		self::thumbnailImg($relativePath ,$fileName ,200,200);
	}

	public static function thumbnailImg($path,$filename, $width, $height, $mode = self::THUMBNAIL_OUTBOUND, $quality = null)
	{
		$filename = FileHelper::normalizePath(Yii::getAlias($path.DIRECTORY_SEPARATOR.$filename));
		try {
			$thumbnailFileUrl = self::thumbnailFileUrl($path,$filename, $width, $height, $mode, $quality);
		} catch (\Exception $e) {
			return static::errorHandler($e, $filename);
		}
		return $thumbnailFileUrl;
	}

	public static function thumbnailFileUrl($path,$filename, $width, $height, $mode = self::THUMBNAIL_OUTBOUND, $quality = null)
	{
		$filename = FileHelper::normalizePath(Yii::getAlias($filename));
		$cacheUrl = $path;
		$thumbnailFilePath = self::thumbnailFile($path,$filename, $width, $height, $mode, $quality);

		preg_match('#[^\\' . DIRECTORY_SEPARATOR . ']+$#', $thumbnailFilePath, $matches);
		$fileName = $matches[0];

		return $cacheUrl . $fileName;
	}

	public static function thumbnailFile($path,$filename, $width, $height, $mode = self::THUMBNAIL_OUTBOUND, $quality = null)
	{
		$filename = FileHelper::normalizePath(Yii::getAlias($filename));

		if (!is_file($filename)) {
			throw new FileNotFoundException("File $filename doesn't exist");
		}

		$thumbnailFileExt = strrchr($filename, '.');
		$len = strlen($filename)-strlen($thumbnailFileExt);
		$thumbnailFileName =  substr($filename,0,$len) .'_'.$width.'_'.$height;
		$thumbnailFile =  $thumbnailFileName . $thumbnailFileExt;

		if (file_exists($thumbnailFile)) {
			if (self::$cacheExpire !== 0 && (time() - filemtime($thumbnailFile)) > self::$cacheExpire) {
				unlink($thumbnailFile);
			} else {
				return $thumbnailFile;
			}
		}
//		if (!is_dir($thumbnailFilePath)) {
//			mkdir($thumbnailFilePath, self::MKDIR_MODE, true);
//		}
		$box = new Box($width, $height);
		$image = Image::getImagine()->open($filename);
		$image = $image->thumbnail($box, $mode);

		$options = [
			'quality' => $quality === null ? self::QUALITY : $quality
		];
		$image->save($thumbnailFile, $options);
		unset($image);
		return $thumbnailFile;
	}


	protected static function errorHandler($error, $filename)
	{
		if ($error instanceof FileNotFoundException) {
			return 'File doesn\'t exist';
		} else {
			Yii::warning("{$error->getCode()}\n{$error->getMessage()}\n{$error->getFile()}");
			return 'Error ' . $error->getCode();
		}
	}
}