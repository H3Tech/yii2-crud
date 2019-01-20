<?php

// TODO: Create shared block render functions instead of duplicating code
$includeFunction = function ($field, $blockType, $settings)
use ($form, $model, $viewPaths, $modelName, $modelNameLabel) {
    $blockPath = 'blocks/' . $blockType . '.php';

    foreach ($viewPaths as $viewPath) {
        $blockFile = $viewPath . DIRECTORY_SEPARATOR . $blockPath;
        if (file_exists($blockFile)) {
            echo $this->renderFile($blockFile, array_merge([
                'form' => $form,
                'model' => $model,
                'field' => $field,
                'viewPaths' => $viewPaths,
                'modelName' => $modelName,
                'modelNameLabel' => $modelNameLabel,
            ], $settings, ['settings' => $settings]));
            break;
        }
    }
};

echo "<h2>$field</h2>";

foreach ($settings as $key => $rule) {
    if (is_numeric($key)) {
        $target = $rule[0];
        $type = $rule[1];
        $settings = isset($rule[2]) ? $rule[2] : [];
    } else {
        $target = $key;
        if (is_array($rule)) {
            $type = $rule[0];
            $settings = isset($rule[1]) ? $rule[1] : [];
        } else {
            $type = $rule;
            $settings = [];
        }
    }

    if (is_array($target)) {
        foreach ($target as $attribute) {
            $includeFunction($attribute, $type, $settings);
        }
    } else {
        $includeFunction($target, $type, $settings);
    }
}
