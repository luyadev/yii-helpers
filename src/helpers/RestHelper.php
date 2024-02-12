<?php

namespace luya\yii\helpers;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;

/**
 * Rest API Helper.
 *
 * @since 1.0.0
 * @author Basil Suter <basil@nadar.io>
 */
class RestHelper
{
    /**
     * Send model errors with correct headers.
     *
     * Helper method to correctly send model errors with the correct response headers.
     *
     * Example return value:
     *
     * ```php
     * Array
     * (
     *     [0] => Array
     *         (
     *             [field] => firstname
     *             [message] => First name cannot be blank.
     *         )
     *     [1] => Array
     *         (
     *             [field] => email
     *             [message] => Email cannot be blank.
     *         )
     * )
     * ```
     *
     * @param \yii\base\Model $model The model to find the first error.
     * @throws \yii\base\InvalidParamException
     * @return array If the model has errors `InvalidParamException` will be thrown, otherwise an array with message and field key.
     */
    public static function sendModelError(Model $model)
    {
        if (!$model->hasErrors()) {
            throw new InvalidParamException('The model has thrown an unknown error.');
        }

        Yii::$app->response->setStatusCode(422, 'Data Validation Failed.');
        $result = [];
        foreach ($model->getFirstErrors() as $name => $message) {
            $result[] = [
                'field' => $name,
                'message' => $message,
            ];
        }

        return $result;
    }

    /**
     * Send Array validation error.
     *
     * Example input:
     *
     * ```php
     * return $this->sendArrayError(['firstname' => 'First name cannot be blank']);
     * ```
     *
     * Example return value:
     *
     * ```php
     * Array
     * (
     *     [0] => Array
     *         (
     *             [field] => firstname
     *             [message] => First name cannot be blank.
     *         )
     * )
     * ```
     * @param array $errors Provide an array with messages. Where key is the field and value the message.
     * @return array Returns an array with field and message keys for each item.
     */
    public static function sendArrayError(array $errors)
    {
        Yii::$app->response->setStatusCode(422, 'Data Validation Failed.');
        $result = [];
        foreach ($errors as $key => $value) {
            $messages = (array) $value;

            foreach ($messages as $msg) {
                $result[] = ['field' => $key, 'message' => $msg];
            }
        }

        return $result;
    }
}
