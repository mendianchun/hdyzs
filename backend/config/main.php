<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
	'language'=> 'zh-CN',
	'modules' => [
		'admin' => [
			'class' => 'mdm\admin\Module',
		],
		//......
	],
	'aliases' => [
		'@mdm/admin' => '@vendor/mdmsoft/yii2-admin',
	],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\Adminuser',
            'enableAutoLogin' => true,
        ],
    	'session'=>[
    			'name'=>'PHPBACKSESSION',
    			'savePath'=>sys_get_temp_dir(),
    	],
    	'request'=>[
    			'cookieValidationKey'=>'sdfjjksloeedf78789judf',
    			'csrfParam'=>'_adminCSRF',
    	],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => false,
            'showScriptName' => false,
        	'suffix'=>'.html',
            'rules' => [
            	'<controller:(post|comment)>s'=>'<controller>/index',
            	'<controller:\w+>/<id:\d+>'=>'<controller>/view',
            	'<controller:\w+>/<id:\d+>/<action:(create|update|delete)>'=>'<controller>/<action>',
            ],
        ],
	    'assetManager' => [
		    'bundles' => [
			    'dmstr\web\AdminLteAsset' => [
				    'skin' => 'skin-blue-light',
			    ],
		    ],
	    ],
	    //authManager有PhpManager和DbManager两种方式,
		//PhpManager将权限关系保存在文件里,这里使用的是DbManager方式,将权限关系保存在数据库.
	    'authManager' => [
		    'class' => 'yii\rbac\DbManager',
		    'defaultRoles' => ['postAdmin'],
	    ],
    ],

	'as access' => [
		'class' => 'mdm\admin\components\AccessControl',
		'allowActions' => [
			//这里是允许访问的action
			//controller/action
			"site/*"
		]

	],

	'on beforeRequest' => function($event) {
		\yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_INSERT, ['backend\components\AdminLog', 'write']);
		\yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_UPDATE, ['backend\components\AdminLog', 'write']);
		\yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_DELETE, ['backend\components\AdminLog', 'write']);
	},

    'params' => $params,
];
