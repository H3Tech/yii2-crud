<?php

namespace h3tech\crud\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "image_crop".
 *
 * @property int $id
 * @property int $image_id
 * @property string $aspect_width
 * @property string $aspect_height
 * @property int $x
 * @property int $y
 * @property int $width
 *
 * @property Image $image
 */
class ImageCrop extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image_crop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_id', 'x', 'y', 'width'], 'required'],
            [['image_id', 'aspect_width', 'aspect_height', 'x', 'y', 'width'], 'integer'],
            [['image_id', 'aspect_width', 'aspect_height'], 'unique', 'targetAttribute' => ['image_id', 'aspect_width', 'aspect_height']],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::class, 'targetAttribute' => ['image_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image_id' => 'Image ID',
            'aspect_width' => 'Aspect Width',
            'aspect_height' => 'Aspect Height',
            'x' => 'X',
            'y' => 'Y',
            'width' => 'Width',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(Image::class, ['id' => 'image_id']);
    }
}
