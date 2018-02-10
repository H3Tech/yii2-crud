<?php

namespace h3tech\crud\models;

use h3tech\crud\Module;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "crud_media".
 *
 * @property integer $id
 * @property string $type
 * @property string $filename
 * @property string $created_at
 *
 * @property string $uploadedUrl
 */
class Media extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        /** @var Module $crudModule */
        $crudModule = Yii::$app->getModule('h3tech-crud');
        return $crudModule->mediaTableName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'filename'], 'required'],
            [['created_at'], 'safe'],
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
            'created_at' => 'Created At',
        ];
    }

    public static function getUploadPath($filename)
    {
        return Yii::getAlias(Module::getInstance()->uploadPath) . $filename;
    }

    public static function getUploadUrl($filename)
    {
        return Yii::getAlias(Module::getInstance()->relativeUploadPath) . $filename;
    }

    public function getUploadedUrl()
    {
        return static::getUploadUrl($this->filename);
    }
}
