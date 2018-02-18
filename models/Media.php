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
 * @property string $uploadedPath
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
        return rtrim(Yii::getAlias(Module::getInstance()->uploadPath), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . $filename;
    }

    public static function getUploadUrl($filename)
    {
        return rtrim(Yii::getAlias(Module::getInstance()->baseUploadUrl), '/') . '/' . $filename;
    }

    public function getUploadedPath()
    {
        return static::getUploadPath($this->filename);
    }

    public function getUploadedUrl()
    {
        return static::getUploadUrl($this->filename);
    }
}
