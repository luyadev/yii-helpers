<?php

namespace luya\yii\tests\helpers;

use luya\console\Importer;
use luya\yii\helpers\ExportHelper;
use luya\yii\helpers\ImportHelper;
use luya\yii\tests\HelpersTestCase;

class ImportHelperTest extends HelpersTestCase
{
    public function testCsvToArray()
    {
        $csv = ExportHelper::csv([['firstname' => 'John', 'lastname' => 'Doe'], ['firstname' => 'Jane', 'lastname' => 'Doe']]);
        
        $this->assertSame([
            0 => ['firstname', 'lastname'],
            1 => ['John', 'Doe'],
            2 => ['Jane', 'Doe'],
        ], ImportHelper::csv($csv));
        
        $this->assertSame([
            0 => ['John', 'Doe'],
            1 => ['Jane', 'Doe'],
        ], ImportHelper::csv($csv, ['removeHeader' => true]));
        
        $this->assertSame([
            0 => ['John'],
            1 => ['Jane'],
        ], ImportHelper::csv($csv, ['removeHeader' => true, 'fields' => [0]]));
        
        $this->assertSame([
            0 => ['lastname'],
            1 => ['Doe'],
            2 => ['Doe'],
        ], ImportHelper::csv($csv, ['fields' => [1]]));
        
        $this->assertSame([
            0 => ['John'],
            1 => ['Jane'],
        ], ImportHelper::csv($csv, ['removeHeader' => true, 'fields' => ['firstname']]));
    }
    
    public function testCsvWithNewline()
    {
        $csv = ExportHelper::csv([['firstname' => 'John', 'text' => 'Hello' . PHP_EOL . 'World'], ['firstname' => 'Jane', 'text' => 'World\nHello']]);
        
        $this->assertSame([
            0 => ['firstname', 'text'],
            1 => ['John', 'Hello' . PHP_EOL . 'World'],
            2 => ['Jane', 'World\nHello'],
        ], ImportHelper::csv($csv));
    }

    public function testResourceImport()
    {
        $resource = fopen('php://memory', 'rw');
        fwrite($resource, 'foobarcontent');
        rewind($resource);

        $result = ImportHelper::csvFromResource($resource);

        $this->assertSame([
            0 => ['foobarcontent'],
        ], $result);
    }
}
