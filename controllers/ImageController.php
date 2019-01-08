<?php

namespace h3tech\crud\controllers;

use Yii;
use h3tech\crud\models\Image;
use h3tech\crud\models\ImageCrop;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\helpers\BaseArrayHelper;

class ImageController extends Controller
{
    /**
     * @param string $id
     * @param array $params
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidRouteException
     */
    public function runAction($id, $params = [])
    {
        $params = BaseArrayHelper::merge(Yii::$app->getRequest()->getBodyParams(), $params);
        return parent::runAction($id, $params);
    }

    public function actionRenderCropper($id, $sizes)
    {
        return $this->renderPartial('cropper-modal', [
            'image' => Image::findOne($id),
            'sizes' => json_decode($sizes, true),
        ]);
    }

    public function actionSaveCrop($id, $aspectWidth, $aspectHeight, $x, $y, $width)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (($image = Image::findOne($id)) === null) {
            throw new NotFoundHttpException();
        }

        // Convert [0,1] to original image size space
        $imageSize = $image->originalSize;
        $x = floor($x * $imageSize['width']);
        $width = floor($width * $imageSize['width']);
        $y = floor($y * $imageSize['height']);

        $image->crop($aspectWidth, $aspectHeight, $x, $y, $width);

        $crop = ImageCrop::findOne([
            'image_id' => $id,
            'aspect_width' => $aspectWidth,
            'aspect_height' => $aspectHeight,
        ]);

        if ($crop === null) {
            $crop = new ImageCrop();
            $crop->aspect_width = $aspectWidth;
            $crop->aspect_height = $aspectHeight;
            $crop->image_id = $image->id;
        }
        $crop->x = $x;
        $crop->y = $y;
        $crop->width = $width;

        $crop->save();
        $crop->refresh();

        ImageCrop::deleteAll(['not', ['id' => $crop->id]]);

        return $crop->attributes;
    }

    public function actionGetCrops($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (($image = Image::findOne($id)) === null) {
            throw new NotFoundHttpException();
        }

        $result = [];

        if (!empty($crops = $image->crops)) {
            $imageSize = $image->originalSize;
            $width = $imageSize['width'];
            $height = $imageSize['height'];

            foreach ($crops as $crop) {
                $resultItem = $crop->attributes;
                $resultItem['x'] /= $width;
                $resultItem['width'] /= $width;
                $resultItem['y'] /= $height;

                $result[] = $resultItem;
            }
        }

        return $result;
    }
}
