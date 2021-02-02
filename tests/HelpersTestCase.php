<?php

namespace luya\yii\tests;

use luya\base\Boot;
use luya\testsuite\cases\BaseTestSuite;

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
$_SERVER['REQUEST_URI'] = '/luya/envs/dev/public_html/';
$_SERVER['SCRIPT_NAME'] = '/luya/envs/dev/public_html/index.php';
$_SERVER['PHP_SELF'] = '/luya/envs/dev/public_html/index.php';
$_SERVER['SCRIPT_FILENAME'] = '/var/www/luya/envs/dev/public_html/index.php';

class HelpersTestCase extends BaseTestSuite
{
    public function getConfigArray()
    {
        return [
            'id' => 'helpers',
            'basePath' => dirname(__DIR__),
            'aliases' => [
                '@runtime' => dirname(__DIR__) . '/tests/data/runtime',
                '@luyatests' => dirname(__DIR__) . '/../',
            ],
            'components' => [
                'sqllite' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
                'composition' => [
                    'hidden' => false,
                ],
                'request' => [
                    'forceWebRequest' => true,
                ],
            ]
        ];
    }
    
    public function bootApplication(Boot $boot)
    {
        $boot->applicationWeb();
    }
}
