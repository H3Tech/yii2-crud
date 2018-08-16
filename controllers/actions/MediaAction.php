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
 * @property MediaController $mediaControllerClass
 */
class MediaAction extends Action
{
    protected $type;
    public $mediaIdAttribute;
    public $fileVariable;
    public $prefix = null;
    protected $mediaControllerClass = null;

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

    public function getMediaControllerClass()
    {
        return $this->mediaControllerClass === null ? MediaController::class : $this->mediaControllerClass;
    }

    public function setMediaControllerClass($mediaControllerClass)
    {
        if (!is_subclass_of($mediaControllerClass, MediaController::class)) {
            throw new InvalidConfigException('Media controller class must be MediaController decendant');
        }

        $this->mediaControllerClass = $mediaControllerClass;
    }

    protected function uploadMedia(ActiveRecord $model, UploadedFile $mediaFile)
    {
        $controllerClass = $this->controllerClass;
        $mediaControllerClass = $this->getMediaControllerClass();

        $model->{$this->mediaIdAttribute} = $mediaControllerClass::upload(
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
