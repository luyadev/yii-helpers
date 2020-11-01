<?php

namespace luya\helpers\tests;

use luya\base\Boot;
use luya\testsuite\cases\BaseTestSuite;

class HelpersTestCase extends BaseTestSuite
{
    public function getConfigArray()
    {
        return [
            'id' => 'helpers',
            'basePath' => dirname(__DIR__),
            'aliases' => [
                '@runtime' => dirname(__DIR__) . '/data/runtime',
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