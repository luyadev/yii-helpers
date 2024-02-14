<?php

namespace luya\yii\helpers;

use Yii;

/**
 * Import from Formats to Array.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class ImportHelper
{
    /**
     * Import a CSV from a resource and return array.
     *
     * @param resource $resource
     * @param array $options See {{luya\yii\helpers\ExportHelper::csv()}} for all options.
     * @return array
     * @since 1.6.0
     */
    public static function csvFromResource($resource, array $options = []): array
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException("The provided resource is not a valid resource.");
        }
        $data = [];
        while (($row = fgetcsv($resource, 0, ArrayHelper::getValue($options, 'delimiter', ','), ArrayHelper::getValue($options, 'enclosure', '"'))) !== false) {
            $data[] = $row;
        }
        fclose($resource);

        // check whether only an amount of fields should be parsed into the final array
        $fields = ArrayHelper::getValue($options, 'fields', false);
        if ($fields && is_array($fields)) {
            $filteredData = [];
            foreach ($fields as $fieldColumn) {
                if (!is_numeric($fieldColumn)) {
                    $fieldColumn = array_search($fieldColumn, $data[0]);
                }
                foreach ($data as $key => $rowValue) {
                    if (array_key_exists($fieldColumn, $rowValue)) {
                        $filteredData[$key][] = $rowValue[$fieldColumn];
                    }
                }
            }

            $data = $filteredData;
            unset($filteredData);
        }

        // if the option to remove a header is provided, remove the first key and re-set array keys
        if (ArrayHelper::getValue($options, 'removeHeader', false)) {
            unset($data[0]);
            $data = array_values($data);
        }

        return $data;
    }

    /**
     * Import a CSV from a string or filename and return array.
     *
     * The filename can be either a resource from `fopen()` or a string containing the CSV data. Filenames will be wrapped through {{`Yii::getAlias()`}} method.
     *
     * @param string $filename Can be either a filename which is parsed by {{luya\yii\helpers\FileHelper::getFileContent()}} or a string with the contained CSV data.
     * @param array $options Provide options to the CSV
     * + `removeHeader`: boolean, Whether the import CSV contains a header in the first row to skip or not. Default value is false.
     * + `delimiter`: string, The delimiter which is used to explode the columns. Default value is `,`.
     * + `enclosure`: string, The enclosure which is used between the columns. Default value is `"`.
     * + `fields`: array, An array with filenames (based on the array header if any, or position) which should be parsed into the final export.
     * ```php
     * 'fields' => ['firstname', 'lastname'] // will only parse those fields based on table header (row 0)
     * 'fields' => [0,1,3] // will only parse fields by those positions if no table header is present. Positions starts at 0.
     * ```
     * @return array an array with the CSV data.
     */
    public static function csv($filename, array $options = [])
    {
        $filename = Yii::getAlias($filename);

        // check if a given file name is provided or a CSV based on the content
        if (FileHelper::getFileInfo($filename)->extension) {
            $resource = fopen($filename, 'r');
        } else {
            $resource = fopen('php://memory', 'rw');
            fwrite($resource, $filename);
            rewind($resource);
        }

        return self::csvFromResource($resource, $options);
    }
}
