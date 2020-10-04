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
        ];
    }
    
    public function bootApplication(Boot $boot)
    {
         $boot->applicationWeb();
    }
}