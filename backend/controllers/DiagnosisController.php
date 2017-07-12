<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2017/6/29
 * Time: 下午3:31
 */
namespace backend\controllers;

use Yii;
use common\models\Appointment;
use common\models\AppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppointmentController implements the CRUD actions for Appointment model.
 */
class DiagnosisController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['POST'],
				],
			],
		];
	}

	/**
	 * Lists all Appointment models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new AppointmentSearch();
		$params = Yii::$app->request->queryParams;
		$params['AppointmentSearch']['dx_status']=Appointment::DX_STATUS_UN;
		$dataProvider = $searchModel->search($params);

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Appointment model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new Appointment model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
//		$model = new Appointment();
//
//		if ($model->load(Yii::$app->request->post()) && $model->save()) {
//			return $this->redirect(['view', 'id' => $model->appointment_no]);
//		} else {
//			return $this->render('create', [
//				'model' => $model,
//			]);
//		}
	}

	/**
	 * Updates an existing Appointment model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
//		$model = $this->findModel($id);
//
//		if ($model->load(Yii::$app->request->post()) && $model->save()) {
//			return $this->redirect(['view', 'id' => $model->appointment_no]);
//		} else {
//			return $this->render('update', [
//				'model' => $model,
//			]);
//		}
	}

	/**
	 * Deletes an existing Appointment model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
//		$this->findModel($id)->delete();
//
//		return $this->redirect(['index']);
	}

	/**
	 * Finds the Appointment model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Appointment the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Appointment::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	public function actionApprove($id)
	{
//		$model = $this->findModel($id);
//		if($model->approve())  //审核
//		{
//			return $this->redirect(['index']);
//		}
	}

	public function actionPay($id)
	{
//		$model = $this->findModel($id);
//		if($model->pay())  //支付
//		{
//			return $this->redirect(['index']);
//		}
	}
}