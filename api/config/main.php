<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',

    'components' => [
        'user' => [
            'class' => yii\web\User::className(),
            'identityClass' => api\models\User::className(),
            'enableSession' => false,
        ],
        'log' => [//此项具体详细配置，请访问http://wiki.feehi.com/index.php?title=Yii2_log
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::className(),//当触发levels配置的错误级别时，保存到日志文件
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/'.date('Y/m/d') . '.log',
                ],
            ],
        ],
        'cache' => [
            'class' => yii\caching\DummyCache::className(),
            'keyPrefix' => 'api',       // 唯一键前缀
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'login' => 'site/login',
                'v1/login' => 'v1/site/login',
                'register' => 'site/register',
                'v1/register' => 'v1/site/register',
                'v1' => 'v1/site/index',
                [
                    'class' => yii\rest\UrlRule::className(),
                    'controller' => ['user', 'article', 'paid'],//通过/users,/user/1,/paid/info访问
                    /*'extraPatterns' => [
                        'GET search' => 'search',
                    ],*/
                ],
                [
                    'class' => yii\rest\UrlRule::className(),//v1版本路由，通过/v1/users,/v1/user/1,/v1/paid/info...访问
                    'controller' => ['v1/site', 'v1/user', 'v1/article', 'v1/paid'],
                ],
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                '<version:v\d+>/<controller:\w+>/<action:\w+>'=>'<version>/<controller>/<action>',
            ],
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'text/json' => 'yii\web\JsonParser',
            ],
            'enableCsrfValidation' => false,
            'enableCookieValidation' => false,
        ],
        'response' => [
            'class' => 'yii\web\Response',
//            'on beforeSend' => function ($event) {
//                $response = $event->sender;
//                if ($response->data !== null) {
//                    $response->data = [
//                        'success' => $response->isSuccessful,
//                        'data' => $response->data,
//                    ];
//                    $response->statusCode = 200;
//                }
//            },
            'format' => null,
            'as format' => [
                'class' => api\behaviors\ResponseFormatBehavior::className(),
            ],
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => api\modules\v1\Module::className(),
        ],
    ],
    'params' => $params,
];
