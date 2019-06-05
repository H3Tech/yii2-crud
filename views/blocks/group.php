<?php

use h3tech\crud\helpers\CrudWidget;

/**
 * @var array $context
 */

echo "<h2>$field</h2>";
CrudWidget::renderFormRules($this, $settings, $context);
