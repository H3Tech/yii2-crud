<?php

use dosamigos\tinymce\TinyMce;
use h3tech\crud\helpers\CrudWidget;

/* @var string $field */

$settings = array_merge(['maxLength' => true], $settings);

$label = isset($settings['label']) ? $settings['label'] : null;
$labelOptions = isset($settings['labelOptions']) ? $settings['labelOptions'] : [];

$disableAutomaticHints = isset($settings['disableAutomaticHints']) ? $settings['disableAutomaticHints'] : false;
$hint = isset($settings['hint']) ? $settings['hint'] : null;
if (!$disableAutomaticHints && $hint === null && $model->getAttributeHint($field) === '') {
    $hint = CrudWidget::getLengthHint($model, $field);
}

$options = isset($settings['options']) ? $settings['options'] : [];
$clientOptions = isset($settings['clientOptions']) ? $settings['clientOptions'] : [];

$languageFolder = Yii::getAlias('@vendor/2amigos/yii2-tinymce-widget/src/assets/langs');
$appLanguage = Yii::$app->language;

$editorLanguage = null;

if ($appLanguage !== 'en') {
    if (strlen($appLanguage) !== 2) {
        $appLanguage = str_replace('-', '_', $appLanguage);
    }

    $languageBaseNames = array_map(function ($fileName) {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }, array_values(preg_grep('/^' . $appLanguage . '(_[A-Z]+)?\.js$/', scandir($languageFolder))));

    if (count($languageBaseNames) > 0) {
        $editorLanguage = $languageBaseNames[0];
    }
}

echo $form->field($model, $field)->widget(TinyMce::class, [
    'language' => $editorLanguage,
    'options' => $options,
    'clientOptions' => array_merge([
        'branding' => false,
        'entity_encoding' => 'raw',
        'plugins' => [
            'advlist', 'autolink', 'lists', 'link', 'preview', 'searchreplace', 'code', 'media', 'table', 'paste',
            'autoresize', 'image',
        ],
        'toolbar' => 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image',
        'relative_urls' => false,
    ], $clientOptions),
])->label($label, $labelOptions)->hint($hint);
