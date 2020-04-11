<?php

namespace h3tech\crud;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * @property string $mediaTableName
 * @property string uploadPath
 * @property string baseUploadUrl
 */
class Module extends \yii\base\Module
{
    public $mediaTableName = 'crud_media';

    protected $uploadPath = '@webroot/uploads/';
    protected $baseUploadUrl = '@web/uploads/';

    public function init()
    {
        parent::init();
        static::registerTranslations();
    }

    protected static function registerTranslations()
    {
        Yii::$app->i18n->translations['h3tech/crud/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@h3tech/crud/messages',
            'fileMap' => [
                'h3tech/crud/crud' => 'crud.php',
            ],
        ];

        Yii::$app->urlManager->addRules([
            'h3tech-crud/<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => 'h3tech-crud/<controller>/<action>',
        ], true);
    }

    protected static function assertPropertyIsString($propertyName, $propertyValue)
    {
        if (!is_string($propertyValue) || trim($propertyValue) === '') {
            throw new InvalidConfigException("The property '$propertyName' must be a non-empty string value if set");
        }
    }

    public function setUploadPath($uploadPath)
    {
        static::assertPropertyIsString('uploadPath', $uploadPath);
        $this->uploadPath = $uploadPath;
    }

    public function setBaseUploadUrl($baseUploadUrl)
    {
        static::assertPropertyIsString('baseUploadUrl', $baseUploadUrl);
        $this->baseUploadUrl = $baseUploadUrl;
    }

    public function getUploadPath()
    {
        return Yii::getAlias($this->uploadPath);
    }

    public function getBaseUploadUrl()
    {
        return Yii::getAlias($this->baseUploadUrl);
    }
}
