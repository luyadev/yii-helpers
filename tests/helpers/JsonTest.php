<?php

namespace luya\yii\tests\helpers;

use luya\yii\helpers\Json;
use luya\yii\tests\HelpersTestCase;

class JsonTest extends HelpersTestCase
{
    public function testIsJson()
    {
        // not a json
        $this->assertFalse(Json::isJson(['foo' => 'bar']));
        $this->assertFalse(Json::isJson('12312312'));
        $this->assertFalse(Json::isJson(12312312));
        $this->assertFalse(Json::isJson('luya{"123":123}'));
        // is a json
        $this->assertTrue(Json::isJson('{"123":"456"}'));
        $this->assertTrue(Json::isJson('{"123":456}'));
        $this->assertTrue(Json::isJson('[{"123":"456"}]'));
        $this->assertTrue(Json::isJson('[{"123":"456"}]'));
    }
}
