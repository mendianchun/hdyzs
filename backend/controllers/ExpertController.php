<?php

namespace backend\controllers;

use Yii;
use backend\models\Expert;
use common\models\ExpertSearch;
use common\models\User;
use common\models\ExpertTime;

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
	//页面时间定义
	public $time_conf = array(  '1_1'=>'周一上午','1_2'=>'周一下午','1_3'=>'周一晚上',
								'2_1'=>'周二上午','2_2'=>'周二下午','2_3'=>'周二晚上',
								'3_1'=>'周三上午','3_2'=>'周三下午','3_3'=>'周三晚上',
								'4_1'=>'周四上午','4_2'=>'周四下午','4_3'=>'周四晚上',
								'5_1'=>'周五上午','5_2'=>'周五下午','5_3'=>'周五晚上',
								'6_1'=>'周六上午','6_2'=>'周六下午','6_3'=>'周六晚上',
								'0_1'=>'周日上午','0_2'=>'周日下午','0_3'=>'周日晚上',);
	public $time_range=array(1=>'8:00-12:00',2=>'14:00-17:00',3=>'19:00-21:00');
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
    	$model = $this->findModel($id);

    	//专家闲时处理
	    $time = $this->freetime($model->free_time);
	    $free_time_str = '';
	    if($time){
	    	foreach($time as $v){
			    $free_time_str.=$this->time_conf[$v].',';
		    }
	    }
		$model->free_time= rtrim($free_time_str,',');
        return $this->render('view', [
            'model' => $model,
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
	    if (Yii::$app->request->post()) {
	    	$post = Yii::$app->request->post();
		    $free_time = array();
		    if(is_array($post['Expert']['free_time'])){
			    foreach($post['Expert']['free_time'] as $v){
				    $keys = explode('_',$v);
				    $free_time[$keys[0]][]=$this->time_range[$keys[1]];

			    }
		    }

		    $expert->free_time =json_encode($free_time);

		    $expert->load(Yii::$app->request->post());
			$res = $expert->newExpert();
			if($res){
				$this->ordertime($res['uuid'],$free_time);
			    return $this->redirect(['view', 'id' => $res['id']]);
			}else{
				return $this->render('create', [
					'model' => $expert,
					'time_conf' => $this->time_conf,
				]);
			}

	    } else {
		    //$expert->free_time=array('1_1','1_2','1_3','2_1','2_2','2_3');
		    return $this->render('create', [
			    'model' => $expert,
			    'time_conf' => $this->time_conf,
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
        if($model->load(Yii::$app->request->post())){
	        $post = Yii::$app->request->post();
	        //增加专家
	        $model->name =$post['Expert']['name'];
	        $model->head_img =$post['Expert']['head_img'];

	        $free_time = array();

	        foreach($post['Expert']['free_time'] as $v){
		        $keys = explode('_',$v);
		        $free_time[$keys[0]][]=$this->time_range[$keys[1]];

	        }
	        $model->free_time =json_encode($free_time);
	        //$expert->free_time =serialize($free_time);


	        $model->fee_per_times =$post['Expert']['fee_per_times'];
	        $model->fee_per_hour =$post['Expert']['fee_per_hour'];
	        $model->skill =$post['Expert']['skill'];
	        $model->introduction =$post['Expert']['introduction'];
	        $model->url =$post['Expert']['url'];
	        $status = Expert::updateAll($model,['id'=>$id]);

	        if($status){

		        $this->ordertime($model->user_uuid,$free_time,'update');

		        return $this->redirect(['view', 'id' => $id]);
	        }

        }else{
			$time = $this->freetime($model->free_time);
	        $model->free_time = $time;
	        return $this->render('update', [
		        'model' => $model,
		        'time_conf' => $this->time_conf,
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


	    $model = $this->findModel($id);
	   $res =  User::deleteAll(['uuid'=>$model->attributes['user_uuid']]);

//	    echo '<pre>';
//	    var_dump($res);
//	    exit();
//        $this->findModel($id)->delete();

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

	/**
	 * @param $time
	 * @return array
	 * 处理预约时间
	 */
    private function freetime($time){
    	$new_time = json_decode($time,true);


    	$result =array();
    	if(is_array($new_time)){
		    foreach($new_time as $k=>$value ){
			    foreach($value as $v ){
				    $key = array_search($v, $this->time_range);
				    $result[]=$k.'_'.$key;
			    }
		    }
	    }

	    return $result;
    }

	public function actionTest(){


    	$s = '{"1":["08:00-11:00","13:00-16:00"],"2":["09:00-11:30","13:00-16:00","20:00-22:00"],"3":["08:00-11:00","13:00-16:00"],"4":["08:00-11:00","13:00-16:00"],"5":["08:00-11:00","13:00-16:00"],"6":["08:00-11:00","13:00-16:00"]}';
    	$uuid = 'ebc3199a-f2a2-40a7-8167-7dc755106fce';
    	$res = $this->ordertime($uuid,json_decode($s,true),'update');
//    	return $res;

	}
	/**
	 * @param $uuid
	 * @param $times
	 * @param string $op
	 * @return bool|int|string
	 * 处理预约表
	 */
    private function ordertime($uuid,$times,$op='add'){

		if($op !='add'){
			$today = date('Y-m-d',time());
			$res = ExpertTime::find()->andWhere(['expert_uuid'=>$uuid,'is_order'=>1])->andWhere(['>','date',$today])->count('id');

			if($res>0){
				return false;
			}else{
				ExpertTime::deleteAll(['and','expert_uuid =:uuid','`date`>:today'],[':uuid'=>$uuid,':today'=>$today]);

			}

		}
		$expert_times=array();
		for($i=1;$i<30;$i++){
			$week = date("w",strtotime("+$i day"));
			if(key_exists($week,$times)){
				$cur_day = date('Y-m-d', strtotime("+$i day"));
				$range = $times[$week];
				foreach($range as $v){

					$tmp = explode('-',$v);
					$start = $tmp[0];
					$end = $tmp[1];

					$start_time = strtotime($cur_day.' '.$start.':00');
					$end_time = strtotime($cur_day.' '.$end.':00');

					for($start_time;$start_time<$end_time;$start_time=$start_time+1800){
						$tmp_time=array();
						$tmp_time['expert_uuid']=$uuid;
						$tmp_time['date']=$cur_day;
						$tmp_time['hour']=(int)date('H',$start_time);
						if($start_time%3600==0){
							$tmp_time['zone']=1;
						}else{
							$tmp_time['zone']=2;
						}
						$tmp_time['is_order']=0;
						$expert_times[]=$tmp_time;
					}

				}

			}

		}
	    $res =  Yii::$app->db->createCommand()->batchInsert(ExpertTime::tableName(),['expert_uuid','date','hour','zone','is_order'],$expert_times)->execute();
		return $res;

    }
}
