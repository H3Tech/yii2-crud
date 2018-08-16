<?php

namespace h3tech\crud\controllers\actions;

use h3tech\crud\controllers\AbstractCRUDController;
use h3tech\crud\controllers\MediaController;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property string $type
 * @property ActiveRecord $junctionModelClass
 * @property string $modelIdAttribute
 * @property string $mediaIdAttribute
 * @property string $prefix
 * @property string $fileVariable
 * @property MediaController $mediaControllerClass
 */
class MultipleMediaAction extends Action
{
    protected $type;
    public $junctionModelClass;
    public $modelIdAttribute;
    public $mediaIdAttribute;
    public $prefix = null;
    public $fileVariable;
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

    protected function uploadMedia(UploadedFile $mediaFile)
    {
        $controllerClass = $this->controllerClass;
        $mediaControllerClass = $this->getMediaControllerClass();

        return $mediaControllerClass::upload(
            $mediaFile,
            $this->type,
            ($this->prefix === null ? $controllerClass::getModelPrefix() : $this->prefix)
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
