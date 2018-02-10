<?php

namespace h3tech\crud;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;

/**
 * @property string $mediaTableName
 * @property string uploadPath
 * @property string relativeUploadPath
 */
class Module extends \yii\base\Module
{
    protected static $defaultMediaTableName = 'crud_media';

    public $mediaTableName = null;

    public $uploadPath = '@webroot/uploads/';
    public $relativeUploadPath = '@web/uploads/';

    public function init()
    {
        parent::init();

        static::registerTranslations();

        if ($this->mediaTableName === null) {
            $this->mediaTableName = static::$defaultMediaTableName;
        }

        if (YII_ENV_DEV) {
            $this->createMediaTableIfNotExists();
        }
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

    protected function createMediaTableIfNotExists()
    {
        if (Yii::$app->db->schema->getTableSchema($this->mediaTableName, true) === null) {
            Yii::$app->db->createCommand(preg_replace(
                '/`' . static::$defaultMediaTableName . '`/',
                '`' . $this->mediaTableName . '`',
                file_get_contents(Yii::getAlias('@h3tech/crud/schema/' . static::$defaultMediaTableName . '.sql'))
            ))->execute();
        }
    }

    protected static function assertPropertyIsString($propertyName, $propertyValue)
    {
        if (!is_string($propertyValue) || trim($propertyValue) === '') {
            throw new InvalidConfigException("The property '$propertyName' must be a non-empty string value if set");
        }
    }

    public function setMediaTableName($mediaTableName)
    {
        static::assertPropertyIsString('mediaTableName', $mediaTableName);
        $this->mediaTableName = $mediaTableName;
    }

    public function actionSetUploadPath($uploadPath)
    {
        static::assertPropertyIsString('uploadPath', $uploadPath);
        $this->uploadPath = $uploadPath;
    }

    public function actionSetRelativeUploadPath($relativeUploadPath)
    {
        static::assertPropertyIsString('relativeUploadPath', $relativeUploadPath);
        $this->relativeUploadPath = $relativeUploadPath;
    }

    public function getUploadPath()
    {
        return Yii::getAlias($this->uploadPath);
    }

    public function getRelativeUploadPath()
    {
        return Yii::getAlias($this->relativeUploadPath);
    }
}
