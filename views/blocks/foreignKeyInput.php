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
$foreignLabel = $foreignModel['label'];
$relatedModels = $foreignModelQuery === null ? $foreignModelClass::find()->all() : $foreignModelQuery->all();

$items = [];
foreach ($relatedModels as $relatedModel) {
    $key = is_callable($foreignKey) ? call_user_func($foreignKey, $relatedModel) : $relatedModel->$foreignKey;
    $label = is_callable($foreignLabel) ? call_user_func($foreignLabel, $relatedModel) : $relatedModel->$foreignLabel;
    $items[$key] = $label;
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
} else {
    $selectedItems[] = $model->$field;
}

echo $form->field($model, $field)->dropDownList($items, array_merge([
    'multiple' => isset($junctionModel),
    'value' => $selectedItems,
    'prompt' => isset($junctionModel) ? null : Yii::t('h3tech/crud/crud', 'None'),
], $options));
