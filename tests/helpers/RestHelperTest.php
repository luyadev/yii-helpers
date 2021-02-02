<?php

namespace luya\yii\helpers\tests\helpers;

use yii\base\DynamicModel;
use luya\yii\helpers\RestHelper;
use luya\yii\helpers\tests\HelpersTestCase;

class RestHelperTest extends HelpersTestCase
{
    public function testSendModelError()
    {
        $model = new DynamicModel(['foo', 'bar']);

        $model->addError('foo', 'error!');

        $this->assertSame([
            [
                'field' => 'foo',
                'message' => 'error!',
            ]
        ], RestHelper::sendModelError($model));
    }

    public function testSendModelWithoutError()
    {
        $model = new DynamicModel(['foo', 'bar']);

        $this->expectException('yii\base\InvalidParamException');
        RestHelper::sendModelError($model);
    }


    public function testSendArrayError()
    {
        $this->assertSame([
            [
                'field' => 'foo',
                'message' => 'error!',
            ]
        ], RestHelper::sendArrayError(['foo' => 'error!']));
    }
}
