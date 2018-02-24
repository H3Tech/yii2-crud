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
        $media = Media::findOne($model[$this->mediaIdAttribute]);
        if ($media !== null) {
            $media->delete();
        }
    }

    protected function uploadMedia(UploadedFile $mediaFile)
    {
        $model[$this->mediaIdAttribute] = MediaController::upload(
            $mediaFile,
            $this->type,
            ($this->prefix === null? AbstractCRUDController::getModelPrefix() : $this->prefix)
        );
    }

    public function create(ActiveRecord $model)
    {
        $mediaFile = UploadedFile::getInstance($model, $this->fileVariable);
        if ($mediaFile !== null) {
            $this->uploadMedia($mediaFile);
        }
    }

    public function update(ActiveRecord $model)
    {
        $mediaFile = UploadedFile::getInstance($model, $this->fileVariable);
        if ($mediaFile !== null) {
            $this->deleteMedia($model);
            $this->uploadMedia($mediaFile);
        }
    }

    public function delete(ActiveRecord $model)
    {
        $this->deleteMedia($model);
    }
}
