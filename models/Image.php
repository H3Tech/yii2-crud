<?php

namespace h3tech\crud\models;

use Yii;
use h3tech\crud\Module;

/**
 * @property ImageCrop[] $crops
 * @property array $size
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

    public function getSize()
    {
        $sizeData = getimagesize($this->filePath);
        return ['width' => $sizeData[0], 'height' => $sizeData[1]];
    }

    public function crop($aspectWidth, $aspectHeight, $x, $y, $width)
    {
        $newImage = \yii\imagine\Image::crop(
            $this->filePath, $width, $width / ($aspectWidth / $aspectHeight), [$x, $y]
        );

        $newImage->save($this->getCroppedFilePath($aspectWidth, $aspectHeight));
    }

    public function getCroppedFilePath($aspectWidth, $aspectHeight)
    {
        return $this->getInternalFilePathBySuffix("{$aspectWidth}-$aspectHeight");
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
}
