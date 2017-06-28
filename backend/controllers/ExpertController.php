<?php

namespace backend\controllers;

use Yii;
use backend\models\Expert;
use common\models\ExpertSearch;
use common\models\User;
use common\components\Upload;

use backend\models\UploadForm;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\service\Service;
use yii\helpers\Json;

/**
 * ExpertController implements the CRUD actions for Expert model.
 */
class ExpertController extends Controller
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
     * Lists all Expert models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExpertSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Expert model.
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
     * Creates a new Expert model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $expert= new Expert();

        $time_conf = array( '1_1'=>'周一上午','1_2'=>'周一下午','1_3'=>'周一晚上',
					        '2_1'=>'周二上午','2_2'=>'周二下午','2_3'=>'周二晚上',
					        '3_1'=>'周三上午','3_2'=>'周三下午','3_3'=>'周三晚上',
					        '4_1'=>'周四上午','4_2'=>'周四下午','4_3'=>'周四晚上',
					        '5_1'=>'周五上午','5_2'=>'周五下午','5_3'=>'周五晚上',
					        '6_1'=>'周六上午','6_2'=>'周六下午','6_3'=>'周六晚上',
					        '7_1'=>'周日上午','7_2'=>'周日下午','7_3'=>'周日晚上',);


	    if (Yii::$app->request->post()) {
	    	$post = Yii::$app->request->post();

	    	//增加用户
		    $user = new User();
		    $user->username = $post['Expert']['username'];
		    $user->mobile = $post['Expert']['mobile'];
		    $user->setPassword($post['Expert']['password']);
		    $user->generateAuthKey();

		    $uuid = Service::create_uuid();

		    $user->uuid = $uuid;
		    if($user->save()){


				//增加专家
			    $expert->name =$post['Expert']['name'];
			    $expert->head_img =$post['Expert']['head_img'];

			    $free_time = array();
			    $time_range=array(1=>'8:00-12:00',2=>'14:00-17:00',3=>'19:00-21:00');
			    foreach($post['Expert']['free_time'] as $v){
				    $keys = explode('_',$v);
				    $free_time[$keys[0]][]=$time_range[$keys[1]];

			    }
			    $expert->free_time =json_encode($free_time);
			    //$expert->free_time =serialize($free_time);


			    $expert->fee_per_times =$post['Expert']['fee_per_times'];
			    $expert->fee_per_hour =$post['Expert']['fee_per_hour'];
			    $expert->skill =$post['Expert']['skill'];
			    $expert->introduction =$post['Expert']['introduction'];
			    $expert->user_uuid =$uuid;
			    if($expert->save()>0){
				    return $this->redirect(['view', 'id' => $expert->id]);
			    }

		    }else{
			    return $this->render('create', [
				    'model' => $expert,
				    'time_conf' => $time_conf,
			    ]);
		    }



	    } else {
		    return $this->render('create', [
			    'model' => $expert,
			    'time_conf' => $time_conf,
		    ]);
	    }




    }

	public function actionUpload(){
		try {
			$model = new Upload();
			$info = $model->upImage();


			$info && is_array($info) ?
				exit(Json::htmlEncode($info)) :
				exit(Json::htmlEncode([
					'code' => 1,
					'msg' => 'error'
				]));


		} catch (\Exception $e) {
			exit(Json::htmlEncode([
				'code' => 1,
				'msg' => $e->getMessage()
			]));
		}
	}

    /**
     * Updates an existing Expert model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Expert model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Expert model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Expert the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Expert::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
