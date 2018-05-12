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

$options = isset($settings['options']) ? $settings['options'] : [];

$foreignModelClass = $foreignModel['className'];
$foreignModelQuery = isset($foreignModel['query']) ? $foreignModel['query'] : null;
$foreignKey = $foreignModel['key'];
$foreignLabel = isset($foreignModel['label']) ? $foreignModel['label'] : null;
$relatedModels = $foreignModelQuery === null ? $foreignModelClass::find()->all() : $foreignModelQuery->all();

$items = [];
foreach ($relatedModels as $relatedModel) {
    if (get_class($relatedModel) !== get_class($model) || $relatedModel->$foreignKey !== $model->primaryKey) {
        $key = is_callable($foreignKey) ? call_user_func($foreignKey, $relatedModel) : $relatedModel->$foreignKey;

        if ($foreignLabel === null) {
            $label = $key;
        } else {
            $label = is_callable($foreignLabel) ? call_user_func($foreignLabel, $relatedModel) : $relatedModel->$foreignLabel;
        }

        $items[$key] = $label;
    }
}

$selectedItems = [];
if (isset($junctionModel)) {
    $modelField = $junctionModel['modelField'];
    $foreignField = $junctionModel['foreignField'];
    $junctionModelClass = $junctionModel['className'];

    if (count($items) > 0) {
        $selectedItems = array_map(
            function ($item) use ($foreignField) {
                return $item[$foreignField];
            },
            $junctionModelClass::find()
                ->select($foreignField)
                ->where([$modelField => $model->primaryKey])
                ->asArray()
                ->all()
        );
    }
} elseif ($model->$field !== null) {
    $selectedItems[] = $model->$field;
}

echo $form->field($model, $field)->dropDownList($items, array_merge([
    'multiple' => isset($junctionModel),
    'value' => $selectedItems,
    'prompt' => isset($junctionModel) ? null : Yii::t('h3tech/crud/crud', 'None'),
], $options, ['disabled' => count($items) === 0]))->hint(null);
