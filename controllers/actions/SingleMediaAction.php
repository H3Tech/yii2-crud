<?php

namespace h3tech\crud\controllers\actions;

use h3tech\crud\controllers\MediaController;
use h3tech\crud\models\Media;
use Throwable;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\web\UploadedFile;

class SingleMediaAction extends MediaAction
{
    /**
     * @throws Exception
     */
    protected function uploadMedia(ActiveRecord $model, UploadedFile $mediaFile)
    {
        /** @var MediaController $mediaControllerClass */
        $mediaControllerClass = $this->getMediaControllerClass();

        $model->{$this->mediaIdAttribute} = $mediaControllerClass::upload(
            $mediaFile,
            $this->type,
            $this->getModelPrefix()
        );
    }

    protected function getMediaFile(ActiveRecord $model)
    {
        return UploadedFile::getInstance($model, $this->fileVariable);
    }

    /**
     * @throws Exception
     */
    public function afterCreate(ActiveRecord $model)
    {
        $mediaFile = $this->getMediaFile($model);
        if ($mediaFile !== null) {
            $this->uploadMedia($model, $mediaFile);
        }
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function afterUpdate(ActiveRecord $model)
    {
        $mediaFile = $this->getMediaFile($model);
        if ($mediaFile !== null) {
            $oldMediaId = $model->{$this->mediaIdAttribute};

            $this->uploadMedia($model, $mediaFile);

            if ($this->shouldDeleteOldMedia() && ($oldMedia = Media::findOne($oldMediaId)) !== null) {
                $model->save();
                $oldMedia->delete();
            }
        }
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function afterDelete(ActiveRecord $model)
    {
        if ($this->shouldDeleteOldMedia() && ($media = Media::findOne($model->{$this->mediaIdAttribute})) !== null) {
            $media->delete();
        }
    }
}
