<?php

namespace h3tech\crud;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;

/**
 * @property string $mediaTableName
 * @property string $uploadPath
 * @property string $baseUploadUrl
 * @property string $modelNamespace
 * @property string $searchModelNamespace
 */
class Module extends \yii\base\Module
{
    public $mediaTableName = 'crud_media';

    protected $uploadPath = '@webroot/uploads/';
    protected $baseUploadUrl = '@web/uploads/';
    protected $modelNamespace = 'app\models';
    protected $searchModelNamespace = 'app\controllers\search';

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

    public function setModelNamespace($modelNamespace)
    {
        static::assertPropertyIsString('modelNamespace', $modelNamespace);
        $this->modelNamespace = $modelNamespace;
    }

    public function getModelNamespace()
    {
        return $this->modelNamespace;
    }

    public function setSearchModelNamespace($searchModelNamespace)
    {
        static::assertPropertyIsString('searchModelNamespace', $searchModelNamespace);
        $this->searchModelNamespace = $searchModelNamespace;
    }

    public function getSearchModelNamespace()
    {
        return $this->searchModelNamespace;
    }
}
