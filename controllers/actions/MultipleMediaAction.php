<?php

namespace h3tech\crud\controllers\actions;

use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property ActiveRecord $junctionModelClass
 * @property string $modelIdAttribute
 */
class MultipleMediaAction extends MediaAction
{
    public $junctionModelClass;
    public $modelIdAttribute;

    protected function uploadMedia(UploadedFile $mediaFile)
    {
        $controllerClass = $this->controllerClass;
        $mediaControllerClass = $this->getMediaControllerClass();

        return $mediaControllerClass::upload(
            $mediaFile,
            $this->type,
            $this->getModelPrefix()
        );
    }

    public function afterCreate(ActiveRecord $model)
    {
        foreach (UploadedFile::getInstances($model, $this->fileVariable) as $mediaFile) {
            $mediaId = $this->uploadMedia($mediaFile);

            /** @var ActiveRecord $junctionModel */
            $junctionModel = new $this->junctionModelClass;
            $junctionModel->{$this->modelIdAttribute} = $model->primaryKey;
            $junctionModel->{$this->mediaIdAttribute} = $mediaId;
            $junctionModel->save();
        }
    }

    public function beforeDelete(ActiveRecord $model)
    {
        $junctionModelClass = $this->junctionModelClass;
        $junctionModelClass::deleteAll([$this->modelIdAttribute => $model->primaryKey]);
    }
}
