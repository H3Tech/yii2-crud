<?php

namespace h3tech\crud\models;

use Yii;
use h3tech\crud\Module;

/**
 * @property ImageCrop[] $crops
 * @property ImageCrop $crop
 * @property array $size
 * @property string $originalFilePath
 * @property string $originalUrl
 * @property array $originalSize
 */
class Image extends Media
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCrops()
    {
        return $this->hasMany(ImageCrop::class, ['image_id' => 'id']);
    }

    /**
     * @return ImageCrop
     */
    public function getCrop()
    {
        return ImageCrop::find()->where(['image_id' => $this->id])->orderBy(['id' => SORT_DESC])->limit(1)->one();
    }

    public function getSize()
    {
        return static::getSizeByFilePath($this->filePath);
    }

    public function getOriginalSize()
    {
        return static::getSizeByFilePath($this->originalFilePath);
    }

    protected static function getSizeByFilePath($filePath)
    {
        $sizeData = getimagesize($filePath);
        return ['width' => $sizeData[0], 'height' => $sizeData[1]];
    }

    public function crop($aspectWidth, $aspectHeight, $x, $y, $width)
    {
        $newImage = \yii\imagine\Image::crop(
            $this->originalFilePath, $width, $width / ($aspectWidth / $aspectHeight), [$x, $y]
        );

        $newImage->save($this->getCroppedFilePath($aspectWidth, $aspectHeight));
    }

    public function getCroppedFilePath($aspectWidth, $aspectHeight)
    {
        return $this->getInternalFilePathBySuffix("{$aspectWidth}-$aspectHeight");
    }

    public function getCroppedFileName($aspectWidth, $aspectHeight)
    {
        return $this->getSuffixedFileName("{$aspectWidth}-$aspectHeight");
    }

    protected function getInternalFilePathBySuffix($suffix = '')
    {
        return Yii::getAlias(
            Module::getInstance()->uploadPath . DIRECTORY_SEPARATOR . $this->getSuffixedFileName($suffix)
        );
    }

    protected function getSuffixedFileName($suffix = '')
    {
        $pathInfo = pathinfo($this->filename);
        return $pathInfo['filename'] . ($suffix === '' ? '' : "_$suffix") . '.' . $pathInfo['extension'];
    }

    public function getFilePath()
    {
        /* @var ImageCrop $crop */
        return ($crop = $this->crop) === null
            ? parent::getFilePath()
            : static::getUploadPath($this->getCroppedFileName($crop->aspect_width, $crop->aspect_height));
    }

    public function getUrl($scheme = true)
    {
        /* @var ImageCrop $crop */
        return ($crop = $this->crop) === null
            ? parent::getUrl($scheme)
            : static::getUploadUrl($this->getCroppedFileName($crop->aspect_width, $crop->aspect_height), $scheme);
    }

    public function getFileName()
    {
        /* @var ImageCrop $crop */
        return ($crop = $this->crop) === null
            ? parent::getFileName()
            : $this->getCroppedFileName($crop->aspect_width, $crop->aspect_height);
    }

    public function getOriginalFilePath()
    {
        return parent::getFilePath();
    }

    public function getOriginalUrl($scheme = true)
    {
        return parent::getUrl($scheme);
    }

    public function getOriginalFileName()
    {
        return parent::getFileName();
    }
}
