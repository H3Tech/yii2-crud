<?php

namespace h3tech\crud\controllers;

use h3tech\crud\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\web\Controller;
use h3tech\crud\models\Media;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\web\Response;

class MediaController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public static function upload(UploadedFile $mediaFile, $type, $prefix)
    {
        if ($prefix == null || trim($prefix) == '') {
            $prefix = $type . '_';
        }
        $fileName = uniqid($prefix) . '_' . $mediaFile->name;

        $mediaFile->saveAs(Media::getUploadPath($fileName));

        $media = new Media();
        $media->type = $type;
        $media->filename = $fileName;
        $media->save();

        return Yii::$app->getDb()->getLastInsertID();
    }

    public static function getSinglePreviewData($mediaId)
    {
        $result = [];
        $result['initialPreview'] = [];
        $result['initialPreviewConfig'] = [];

        $media = Media::findOne($mediaId);

        if ($media !== null) {
            $result['initialPreview'][] = $media->uploadedUrl;
            $result['initialPreviewConfig'][] = [
                'type' => $media->type,
                'filetype' => FileHelper::getMimeType($media->uploadedPath),
                'caption' => $media->filename,
            ];
        }

        return $result;
    }

    protected static function getMultiplePreviewConfig(Media $media, $junctionModelClass, $mediaIdAttribute)
    {
        return [
            'type' => $media->type,
            'filetype' => FileHelper::getMimeType($media->uploadedPath),
            'caption' => $media->filename,
            'url' => Url::to(['/media/delete']),
            'key' => $media->primaryKey,
            'extra' => [
                'junctionModelClass' => $junctionModelClass,
                'mediaIdAttribute' => $mediaIdAttribute,
                'mediaId' => $media->primaryKey,
            ],
        ];
    }

    public static function getMultiplePreviewData($modelId, $junctionModelClass, $modelIdAttribute, $mediaIdAttribute, $orderAttribute = null)
    {
        $result = [];
        $result['initialPreview'] = [];
        $result['initialPreviewConfig'] = [];

        $junctionQuery = $junctionModelClass::find()->where([$modelIdAttribute => $modelId]);

        if ($orderAttribute !== null) {
            $junctionQuery->orderBy([$orderAttribute => SORT_ASC]);
        }
        $junctionEntries = $junctionQuery->all();

        foreach ($junctionEntries as $junctionEntry) {
            $media = Media::findOne($junctionEntry->$mediaIdAttribute);

            $result['initialPreview'][] = $media->uploadedUrl;
            $result['initialPreviewConfig'][] = static::getMultiplePreviewConfig(
                $media, $junctionModelClass, $mediaIdAttribute
            );
        }

        return $result;
    }

    public function runAction($id, $params = [])
    {
        $params = array_merge($params, Yii::$app->request->post());
        return parent::runAction($id, $params);
    }

    public static function actionUpload($junctionModelClass, $type, $modelIdAttribute, $mediaIdAttribute, $modelId,
                                        $modelName, $prefix = null)
    {
        $response = [];

        $modelName = str_replace(' ', '', $modelName);
        if ($prefix === null || $prefix === 'null') {
            $prefix = preg_replace('/\s/', '', strtolower($modelName)) . '_';
        }

        $files = UploadedFile::getInstancesByName($modelName);

        $mediaId = static::upload($files[0], $type, $prefix);

        $junctionRecord = new $junctionModelClass;
        $junctionRecord->$modelIdAttribute = $modelId;
        $junctionRecord->$mediaIdAttribute = $mediaId;
        $junctionRecord->save();

        $media = Media::findOne($mediaId);

        $initialPreview = $media->uploadedUrl;
        $initialPreviewConfig = static::getMultiplePreviewConfig($media, $junctionModelClass, $mediaIdAttribute);

        $response['initialPreview'] = [$initialPreview];
        $response['initialPreviewConfig'] = [$initialPreviewConfig];

        $response['result'] = 'ok';

        return $response;
    }

    public static function actionDelete($junctionModelClass, $mediaId, $mediaIdAttribute)
    {
        $junctionModelClass::deleteAll([
            $mediaIdAttribute => $mediaId,
        ]);

        return ['result' => 'ok'];
    }

    public function actionOrder($junctionModelClass, $mediaIdAttribute, $orderAttribute, array $mediaIds)
    {
        for ($i = 0; $i < count($mediaIds); $i++) {
            $junctionEntry = $junctionModelClass::findOne([$mediaIdAttribute => $mediaIds[$i]]);

            if ($junctionEntry->$orderAttribute != $i) {
                $junctionEntry->$orderAttribute = $i;
                $junctionEntry->save();
            }
        }

        return ['result' => 'ok'];
    }
}
