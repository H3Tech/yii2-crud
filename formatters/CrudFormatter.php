<?php

namespace h3tech\crud\formatters;

use yii\i18n\Formatter;

class CrudFormatter extends Formatter
{
    public function asJson($value)
    {
        return $value === null
            ? $this->nullDisplay
            : '<pre>' . json_encode(json_decode($value), JSON_PRETTY_PRINT) . '</pre>';
    }
}
