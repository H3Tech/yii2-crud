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

    protected function deleteMedia(ActiveRecord $model)
    {
        $mediaIdAttribute = $this->mediaIdAttribute;

        $mediaId = $model->$mediaIdAttribute;

        $model->$mediaIdAttribute = null;
        $model->save();

        if (($media = Media::findOne($mediaId)) !== null) {
            $media->delete();
        }
    }

    protected function uploadMedia(ActiveRecord $model, UploadedFile $mediaFile)
    {
        $controllerClass = $this->controllerClass;
        $mediaIdAttribute = $this->mediaIdAttribute;

        $model->$mediaIdAttribute = MediaController::upload(
            $mediaFile,
            $this->type,
            ($this->prefix === null ? $controllerClass::getModelPrefix() : $this->prefix)
        );
    }

    public function create(ActiveRecord $model)
    {
        $mediaFile = UploadedFile::getInstance($model, $this->fileVariable);
        if ($mediaFile !== null) {
            $this->uploadMedia($model, $mediaFile);
        }
    }

    public function update(ActiveRecord $model)
    {
        $mediaFile = UploadedFile::getInstance($model, $this->fileVariable);
        if ($mediaFile !== null) {
            $this->deleteMedia($model);
            $this->uploadMedia($model, $mediaFile);
        }
    }

    public function delete(ActiveRecord $model)
    {
        $this->deleteMedia($model);
    }
}
