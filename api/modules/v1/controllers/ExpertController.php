<?php
namespace api\modules\v1\controllers;

use yii;
use api\modules\ApiBaseController;
use common\models\Expert;
use common\models\ExpertSearch;
use common\models\ExpertTimeSearch;
use common\models\SystemConfig;

use yii\helpers\ArrayHelper;
use common\service\Service;


class ExpertController extends ApiBaseController
{
    public $modelClass = 'common\models\Expert';//对应的数据模型处理控制器

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => [
                	'check',
                ],
            ]
        ]);
    }

    public function actionSearch()
    {
        $queryParam = Yii::$app->request->queryParams;
        $pageSize = isset($queryParam['size']) ? $queryParam['size'] : Yii::$app->params['list.pagesize'];

        $params['ExpertSearch']['name'] = isset($queryParam['name']) ? $queryParam['name'] : null;

        $userSearch = new ExpertSearch();
        $provider = $userSearch->search($params,$pageSize);
        $data = $provider->getModels();



        $conf = SystemConfig::findOne(['name'=>'fee2score']);
		$fee2score = $conf->attributes['value'];

        foreach($data as $k => &$v){
	        $v = $v->attributes;
            if(!empty($v['free_time'])){
                $v['free_time'] = json_decode($v['free_time'],true);
            }
            $v['head_img'] = rtrim(Yii::$app->params['domain'],'/').'/'.$v['head_img'];

	        $v['fee_by_score'] = $v['fee_per_times'] * $fee2score;
	        $v['fee_by_money'] = $v['fee_per_times'] * $fee2score;

        }

        $totalPage = ceil($provider->totalCount / $pageSize);

        if(!isset($queryParam['page']) || $queryParam['page'] <= 0){
            $currentPage = 1;
        }else{
            $currentPage = $queryParam['page'] >= $totalPage ? $totalPage : $queryParam['page'];
        }

        $data['extraFields']['currentPage'] = $currentPage;
        $data['extraFields']['totalCount'] = $provider->totalCount;
        $data['extraFields']['totalPage'] = $totalPage;
        return Service::sendSucc($data);
    }

    public function actionCheck()
    {
	    $queryParam = Yii::$app->request->queryParams;
	    if(!isset($queryParam['expert_uuid']) ||!isset($queryParam['date'])){
		    return Service::sendError(20901,'缺少参数');
	    }

	    $params['ExpertTimeSearch']['expert_uuid'] =$queryParam['expert_uuid'];
	    $params['ExpertTimeSearch']['date'] = $queryParam['date'] ;
	    $params['ExpertTimeSearch']['is_order'] = 0 ;

	    $timeSearch = new ExpertTimeSearch();
	    $provider = $timeSearch->search($params,100);
	    $data = $provider->getModels();
	    $result = array();
		if (count($data)>0){
			foreach($data as $v ){
				$hour=$v->attributes['hour'];
				$desc = Yii::$app->params['time.'.$hour];
				$zone=$v->attributes['zone'];

				$result[$desc][] = $hour.':'.Yii::$app->params['zone.'.$zone.'.start'].'-'.$hour.':'.Yii::$app->params['zone.'.$zone.'.end'];

			}
		}else{
			return Service::sendError(20902,'无空闲时间');
		}

	    return Service::sendSucc($result);
    }

    public function actionTest(){
        $array = array(
            "1" => ['08:00-11:00','13:00-16:00'],
            "2" => ['09:00-11:30','13:00-16:00','20:00-22:00'],
            "3" => ['08:00-11:00','13:00-16:00'],
            "4" => ['08:00-11:00','13:00-16:00'],
            "5" => ['08:00-11:00','13:00-16:00'],
            "6" => ['08:00-11:00','13:00-16:00'],
        );

        echo json_encode($array);
    }
}  