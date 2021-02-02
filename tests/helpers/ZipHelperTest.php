<?php

namespace luya\yii\helpers\tests\helpers;

use luya\yii\helpers\tests\HelpersTestCase;
use Yii;
use luya\yii\helpers\ZipHelper;

class ZipHelperTest extends HelpersTestCase
{
    public function testZipDir()
    {
        $this->assertTrue(ZipHelper::dir(__DIR__, Yii::getAlias('@runtime/test.zip')));
        $this->assertTrue(is_file(Yii::getAlias('@runtime/test.zip')));
    }
}
