<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    	'authManager' => [
    			'class' =>'yii\rbac\DbManager',
    	],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rm-2ze161f04550y64bt.mysql.rds.aliyuncs.com;dbname=hdyzs',
            'username' => 'handian',
            'password' => 'cu7CheW4',
            'charset' => 'utf8',
        ],
    ],
    'timeZone'=>'Asia/Shanghai',
    'language' => 'zh-CN',
];
