<?php

namespace h3tech\crud;

use Yii;
use h3tech\crud\models\Media;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        static::registerTranslations();

        if (YII_ENV_DEV) {
            static::createMediaTableIfNotExists();
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

    protected static function createMediaTableIfNotExists()
    {
        $tableName = Media::tableName();

        if (Yii::$app->db->schema->getTableSchema($tableName, true) === null) {
            Yii::$app->db->createCommand(
                file_get_contents(Yii::getAlias("@h3tech/crud/schema/$tableName.sql"))
            )->execute();
        }
    }
}
