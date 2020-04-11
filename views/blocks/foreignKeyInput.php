<?php
/**
 * @var \yii\widgets\ActiveForm $form
 * @var \yii\db\ActiveRecord $model
 * @var string $field
 * @var array $foreignModel
 * @var \yii\db\ActiveRecord $foreignModelClass
 * @var \yii\db\ActiveQuery $foreignModelQuery
 * @var \yii\db\ActiveRecord $relatedModels
 * @var string $foreignKey
 * @var string $foreignLabel
 * @var array $junctionModel
 * @var string $modelField
 * @var string $foreignField
 */

$label = isset($settings['label']) ? $settings['label'] : null;
$labelOptions = isset($settings['labelOptions']) ? $settings['labelOptions'] : [];

$hint = isset($settings['hint']) ? $settings['hint'] : null;

$options = isset($settings['options']) ? $settings['options'] : [];

$showKeyInList = isset($settings['showKeyInList']) ? $settings['showKeyInList'] : false;
$checkboxList = isset($settings['checkboxList']) ? $settings['checkboxList'] : false;

$items = isset($settings['items']) ? $settings['items'] : [];
$manualItems = false;
if (empty($items)) {
    $foreignModelClass = $foreignModel['className'];
    $foreignModelQuery = isset($foreignModel['query']) ? $foreignModel['query'] : null;
    $foreignKey = $foreignModel['key'];
    $foreignLabel = isset($foreignModel['label']) ? $foreignModel['label'] : null;
    $relatedModels = $foreignModelQuery === null ? $foreignModelClass::find()->all() : $foreignModelQuery->all();

    foreach ($relatedModels as $relatedModel) {
        if (get_class($relatedModel) !== get_class($model) || $relatedModel->$foreignKey !== $model->primaryKey) {
            $key = is_callable($foreignKey) ? call_user_func($foreignKey, $relatedModel) : $relatedModel->$foreignKey;

            if ($foreignLabel === null) {
                $finalLabel = '';
            } else {
                $finalLabel = is_callable($foreignLabel)
                    ? call_user_func($foreignLabel, $relatedModel)
                    : $relatedModel->$foreignLabel;
            }

            $items[$key] = $finalLabel === '' ? $key : (($showKeyInList ? "$key - " : '') . $finalLabel);
        }
    }
} else {
    $manualItems = true;
}

$selectedItems = [];
if (isset($junctionModel)) {
    $modelField = $junctionModel['modelField'];
    $foreignField = $junctionModel['foreignField'];
    $junctionModelClass = $junctionModel['className'];

    if (count($items) > 0) {
        $selectedItems = $model->$field === null
            ? array_map(
                function ($item) use ($foreignField) {
                    return $item[$foreignField];
                },
                $junctionModelClass::find()
                    ->select($foreignField)
                    ->where([$modelField => $model->primaryKey])
                    ->asArray()
                    ->all()
            )
            : $model->$field;
    }
} elseif ($model->$field !== null) {
    $selectedItems[] = $model->$field;
} elseif (isset($foreignModel['defaultKey'])) {
    $selectedItems[] = $foreignModel['defaultKey'];
}

$itemOptions = [];
$disabledKeys = isset($foreignModel['disabledKeys']) ? $foreignModel['disabledKeys'] : [];
foreach ($disabledKeys as $key) {
    $itemOptions[$key] = ['disabled' => true];
}

if ($checkboxList && isset($junctionModel)) {
    echo $form->field($model, $field)->checkboxList($items, ['value' => $selectedItems])->label($label, $labelOptions);
} else {
    echo $form->field($model, $field)->dropDownList($items, array_merge([
        'multiple' => isset($junctionModel),
        'value' => $manualItems && isset($junctionModel) ? array_keys(array_intersect($items, $selectedItems)) : $selectedItems,
        'prompt' => isset($junctionModel) ? null : Yii::t('h3tech/crud/crud', 'None'),
        'options' => $itemOptions,
    ], $options, ['disabled' => count($items) === 0]))->hint($hint)->label($label, $labelOptions);
}
