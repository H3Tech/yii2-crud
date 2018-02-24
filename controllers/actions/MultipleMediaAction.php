<?php

namespace h3tech\crud\controllers\actions;

use h3tech\crud\controllers\AbstractCRUDController;
use h3tech\crud\controllers\MediaController;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property string $type
 * @property ActiveRecord $junctionModelClass
 * @property string $modelIdAttribute
 * @property string $mediaIdAttribute
 * @property string $prefix
 * @property string $fileVariable
 */
class MultipleMediaAction extends Action
{
    protected $type;
    public $junctionModelClass;
    public $modelIdAttribute;
    public $mediaIdAttribute;
    public $prefix = null;
    public $fileVariable;

    protected function uploadMedia(UploadedFile $mediaFile)
    {
        return MediaController::upload(
            $mediaFile,
            $this->type,
            ($this->prefix === null ? AbstractCRUDController::getModelPrefix() : $this->prefix)
        );
    }

    public function create(ActiveRecord $model)
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

    public function delete(ActiveRecord $model)
    {
        ($this->junctionModelClass)::deleteAll([$this->modelIdAttribute => $model->primaryKey]);
    }
}
