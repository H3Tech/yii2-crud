<?php

namespace h3tech\crud\formatters;

use yii\i18n\Formatter;

class CrudFormatter extends Formatter
{
    public function asJson($value)
    {
        $result = $value;

        if ($value === null) {
            $this->nullDisplay;
        } elseif (($json = json_decode($value, true)) !== null) {
            '<pre>' . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . '</pre>';
        }

        return $result;
    }
}
