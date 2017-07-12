<?php

namespace h3tech\crud;

use Yii;

/**
 * @property string $mediaTableName
 */
class Module extends \yii\base\Module
{
    protected static $defaultMediaTableName = 'crud_media';
    protected $mediaTableName = null;

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

    public function setMediaTableName($mediaTableName)
    {
        if (is_string($mediaTableName) && !empty(trim($mediaTableName))) {
            $this->mediaTableName = $mediaTableName;
        } else {
            throw new InvalidConfigException("The property 'mediaTableName' must be a non-empty string value if set");
        }
    }

    public function getMediaTableName()
    {
        return $this->mediaTableName;
    }
}
