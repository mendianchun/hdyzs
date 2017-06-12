<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module'
        ],
        'oauth2' => [
            'class' => 'filsh\yii2\oauth2server\Module',
            'tokenParamName' => 'token',
            'tokenAccessLifetime' => 3600 * 24,
            'storageMap' => [
                'user_credentials' => 'api\models\User',
            ],
            'grantTypes' => [
                'user_credentials' => [
                    'class' => 'OAuth2\GrantType\UserCredentials',
                ],
                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true
                ]
            ]
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'GET v1/<controller:\w+>' => 'v1/<controller>/index',
                'GET v1/<controller:\w+>/<action:\w+>' => 'v1/<controller>/<action>',
                'POST v1/<controller:\w+>/<action:\w+>' => 'v1/<controller>/<action>',

                'POST oauth2/<action:\w+>' => 'oauth2/rest/<action>',
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
//                $response->data = [
//                    'code' => $response->getStatusCode(),
//                    'data' => $response->data,
//                    'message' => $response->statusText
//                ];

                if(isset($response->data['code']) && $response->data['code'] != 0){
                    $response->data['data'] = [];
                }

                if(!isset($response->data['data'])){
                    $response->data['data'] = $response->data;
                }

                if(!isset($response->data['code'])){
                    $response->data['code'] = 0;
                }

                if(!isset($response->data['message'])){
                    $response->data['message'] = 'succ';
                }

                if($response->getStatusCode() != 200){
                    $response->data = [
                        'code' => '10'.$response->getStatusCode(),
                        'data' => [],
                        'message' => $response->data['message']
                    ];
                }else{
                    $response->data = [
                        'code' => $response->data['code'],
                        'data' => $response->data['data'],
                        'message' => $response->data['message'],
                    ];
                }
                $response->statusCode = 200;
                $response->format = yii\web\Response::FORMAT_JSON;
            },
        ],
    ],
    'params' => $params,
];
