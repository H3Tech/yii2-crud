<?php

namespace h3tech\crud\controllers\actions;

use h3tech\crud\models\Media;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class SingleMediaAction extends MediaAction
{
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

    protected function getMediaFile(ActiveRecord $model)
    {
        return UploadedFile::getInstance($model, $this->fileVariable);
    }

    public function afterCreate(ActiveRecord $model)
    {
        $mediaFile = $this->getMediaFile($model);
        if ($mediaFile !== null) {
            $this->uploadMedia($model, $mediaFile);
        }
    }

    public function afterUpdate(ActiveRecord $model)
    {
        $mediaFile = $this->getMediaFile($model);
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
