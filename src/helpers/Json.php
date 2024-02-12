<?php

namespace luya\yii\helpers;

use yii\helpers\BaseJson;

/**
 * JSON Helper.
 *
 * Extends the {{yii\helpers\Json}} class by some useful functions like:
 *
 * + {{luya\yii\helpers\Json::isJson()}}
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Json extends BaseJson
{
    /**
     * Checks if a string is a JSON or not.
     *
     * Example values which return `true`:
     *
     * ```php
     * Json::isJson('{"123":"456"}');
     * Json::isJson('{"123":456}');
     * Json::isJson('[{"123":"456"}]');
     * Json::isJson('[{"123":"456"}]');
     * ```
     *
     * @param mixed $value The value to test if it's a JSON or not.
     * @return boolean Whether the string is a JSON or not.
     */
    public static function isJson($value)
    {
        if (!is_scalar($value)) {
            return false;
        }

        $firstChar = substr($value, 0, 1);

        if ($firstChar !== '{' && $firstChar !== '[') {
            return false;
        }

        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Decode JSON without Exception
     *
     * Since `Json::decode('foo')` would throw an exception, this method will return a default value
     * defined instead of an exception.
     *
     * @param string $json
     * @param boolean $asArray
     * @param mixed $defaultValue
     * @return mixed
     * @since 1.4.0
     */
    public static function decodeSilent($json, $asArray = true, $defaultValue = null)
    {
        try {
            return self::decode($json, $asArray);
        } catch (\Exception $e) {
            return $defaultValue;
        }
    }
}
