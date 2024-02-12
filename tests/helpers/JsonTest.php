<?php

namespace luya\yii\tests\helpers;

use luya\yii\helpers\Json;
use luya\yii\tests\HelpersTestCase;
use yii\base\InvalidArgumentException;

class JsonTest extends HelpersTestCase
{
    public function testIsJson()
    {
        // not a JSON
        $this->assertFalse(Json::isJson(['foo' => 'bar']));
        $this->assertFalse(Json::isJson('12312312'));
        $this->assertFalse(Json::isJson(12312312));
        $this->assertFalse(Json::isJson('luya{"123":123}'));
        $this->assertFalse(Json::isJson('{"123":\'123}'));
        $this->assertFalse(Json::isJson('{"1233}'));
        $this->assertFalse(Json::isJson('{"1232"3}'));
        // is a JSON
        $this->assertTrue(Json::isJson('{"123":"456"}'));
        $this->assertTrue(Json::isJson('{"123":456}'));
        $this->assertTrue(Json::isJson('[{"123":"456"}]'));
        $this->assertTrue(Json::isJson('[{"123":"456"}]'));
    }

    public function testDecodeException()
    {
        $this->expectException(InvalidArgumentException::class);
        Json::decode('dfdf', true);
    }

    public function testDecodeSilent()
    {
        $this->assertNull(Json::decodeSilent('dfdf', true));
        $this->assertFalse(Json::decodeSilent('dfdf', true, false));
        $this->assertSame(['foo' => 'bar'], Json::decodeSilent('{"foo":"bar"}'));
    }
}
