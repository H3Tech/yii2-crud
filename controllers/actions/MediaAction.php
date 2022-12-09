<?php

namespace h3tech\crud\controllers\actions;

use h3tech\crud\controllers\MediaController;
use yii\base\InvalidConfigException;

/**
 * @property string $type
 * @property string $mediaIdAttribute
 * @property string $fileVariable
 * @property string $prefix
 * @property MediaController $mediaControllerClass
 */
abstract class MediaAction extends Action
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
        $supportedTypes = ['image', 'video', 'file'];

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

    protected function getModelPrefix()
    {
        $controllerClass = $this->controllerClass;
        return $this->prefix === null ? $controllerClass::getModelPrefix() : $this->prefix;
    }
}
