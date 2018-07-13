<?php

namespace h3tech\crud\controllers\actions;

use h3tech\crud\controllers\AbstractCRUDController;
use h3tech\crud\controllers\MediaController;
use h3tech\crud\models\Media;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property string $type
 * @property string $mediaIdAttribute
 * @property string $fileVariable
 * @property string $prefix
 */
class MediaAction extends Action
{
    protected $type;
    public $mediaIdAttribute;
    public $fileVariable;
    public $prefix = null;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $supportedTypes = ['image', 'video'];

        if (!in_array($type, $supportedTypes)) {
            throw new InvalidConfigException('Unsupported media type');
        }

        $this->type = $type;
    }

    protected function uploadMedia(ActiveRecord $model, UploadedFile $mediaFile)
    {
        $controllerClass = $this->controllerClass;

        $model->{$this->mediaIdAttribute} = MediaController::upload(
            $mediaFile,
            $this->type,
            ($this->prefix === null ? $controllerClass::getModelPrefix() : $this->prefix)
        );
    }

    public function afterCreate(ActiveRecord $model)
    {
        $mediaFile = UploadedFile::getInstance($model, $this->fileVariable);
        if ($mediaFile !== null) {
            $this->uploadMedia($model, $mediaFile);
        }
    }

    public function afterUpdate(ActiveRecord $model)
    {
        $mediaFile = UploadedFile::getInstance($model, $this->fileVariable);
        if ($mediaFile !== null) {
            $oldMedia = Media::findOne($model->{$this->mediaIdAttribute});

            $this->uploadMedia($model, $mediaFile);
            $model->save();

            if ($oldMedia !== null) {
                $oldMedia->delete();
            }
        }
    }

    public function afterDelete(ActiveRecord $model)
    {
        if (($media = Media::findOne($model->{$this->mediaIdAttribute})) !== null) {
            $media->delete();
        }
    }
}
