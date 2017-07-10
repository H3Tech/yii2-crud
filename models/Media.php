<?php

namespace h3tech\crud\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "media".
 *
 * @property integer $id
 * @property string $type
 * @property string $filename
 * @property string $created
 */
class Media extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'filename'], 'required'],
            [['created'], 'safe'],
            [['type'], 'string', 'max' => 50],
            [['filename'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'filename' => 'Filename',
            'created' => 'Created',
        ];
    }
}
