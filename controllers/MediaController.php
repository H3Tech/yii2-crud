<?php

namespace h3tech\crud\controllers;

use h3tech\crud\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
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
        $fileName = uniqid($prefix) . '_' . static::slug($mediaFile->name);

        $mediaFile->saveAs(Media::getUploadPath($fileName));

        $media = new Media();
        $media->type = $type;
        $media->filename = $fileName;
        $media->save();

        return Yii::$app->db->lastInsertID;
    }

    protected static function slug($string, $replacement = '-', $lowercase = true)
    {
        $string = Inflector::transliterate($string);
        $string = preg_replace('/[^\.a-zA-Z0-9=\s—–-]+/u', '', $string);
        $string = preg_replace('/[=\s—–-]+/u', $replacement, $string);
        $string = trim($string, $replacement);

        return $lowercase ? strtolower($string) : $string;
    }

    public static function getSinglePreviewData($mediaId, $mediaIdAttribute = null, $modelClass = null,
                                                $allowDeletion = true)
    {
        $result = [];
        $result['initialPreview'] = [];
        $result['initialPreviewConfig'] = [];

        $media = Media::findOne($mediaId);

        if ($media !== null) {
            $result['initialPreview'][] = $media->url;

            $previewConfig = [
                'type' => $media->type,
                'filetype' => FileHelper::getMimeType($media->filePath),
                'caption' => $media->filename,
            ];
            if ($mediaIdAttribute !== null && $modelClass !== null && $allowDeletion) {
                $previewConfig = array_merge($previewConfig, [
                    'url' => Url::to(['/h3tech-crud/media/delete-single']),
                    'key' => $media->primaryKey,
                    'extra' => [
                        'modelClass' => $modelClass,
                        'mediaIdAttribute' => $mediaIdAttribute,
                    ],
                ]);
            }

            $result['initialPreviewConfig'][] = $previewConfig;
        }

        return $result;
    }

    protected static function getMultiplePreviewConfig(Media $media, $junctionModelClass, $mediaIdAttribute)
    {
        return [
            'type' => $media->type,
            'filetype' => FileHelper::getMimeType($media->filePath),
            'caption' => $media->filename,
            'url' => Url::to(['/h3tech-crud/media/delete-multiple']),
            'key' => $media->primaryKey,
            'extra' => [
                'junctionModelClass' => $junctionModelClass,
                'mediaIdAttribute' => $mediaIdAttribute,
            ],
        ];
    }

    public static function getMultiplePreviewData($modelId, $junctionModelClass, $modelIdAttribute, $mediaIdAttribute,
                                                  $orderAttribute = null)
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

            $result['initialPreview'][] = $media->url;
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

    public static function actionUploadMultiple($junctionModelClass, $type, $modelIdAttribute, $mediaIdAttribute, $modelId,
                                                $modelName, $prefix = null)
    {
        $response = [];

        $modelName = str_replace(' ', '', $modelName);
        if ($prefix === null || $prefix === 'null') {
            $prefix = preg_replace('/\s/', '', strtolower($modelName)) . '_';
        }

        $files = UploadedFile::getInstancesByName($modelName);

        if (count($files) > 0) {
            $file = $files[0];

            if ($file->hasError) {
                throw new BadRequestHttpException(
                    Yii::t(
                        'h3tech/crud/crud',
                        'File upload failed with error code {errorCode}',
                        ['errorCode' => $file->error]
                    )
                );
            } else {
                $mediaId = static::upload($file, $type, $prefix);

                $junctionRecord = new $junctionModelClass;
                $junctionRecord->$modelIdAttribute = $modelId;
                $junctionRecord->$mediaIdAttribute = $mediaId;
                $junctionRecord->save();

                $media = Media::findOne($mediaId);

                $initialPreview = $media->url;
                $initialPreviewConfig = static::getMultiplePreviewConfig($media, $junctionModelClass, $mediaIdAttribute);

                $response['initialPreview'] = [$initialPreview];
                $response['initialPreviewConfig'] = [$initialPreviewConfig];

                $response['result'] = 'ok';
            }
        } else {
            throw new BadRequestHttpException(Yii::t('h3tech/crud/crud', 'No uploaded file received'));
        }

        return $response;
    }

    public static function actionDeleteSingle($modelClass, $key, $mediaIdAttribute)
    {
        $modelClass::updateAll([$mediaIdAttribute => null], [$mediaIdAttribute => $key]);

        if (($media = Media::findOne($key)) !== null) {
            $media->delete();
        }

        return ['result' => 'ok'];
    }

    public static function actionDeleteMultiple($junctionModelClass, $key, $mediaIdAttribute)
    {
        $junctionModelClass::deleteAll([
            $mediaIdAttribute => $key,
        ]);

        if (($media = Media::findOne($key)) !== null) {
            $media->delete();
        }

        return ['result' => 'ok'];
    }

    public function actionOrder($junctionModelClass, $mediaIdAttribute, $orderAttribute, array $mediaIds)
    {
        for ($i = 0; $i < count($mediaIds); $i++) {
            $junctionEntry = $junctionModelClass::findOne([$mediaIdAttribute => $mediaIds[$i]]);

            if ($junctionEntry->$orderAttribute !== $i) {
                $junctionEntry->$orderAttribute = $i;
                $junctionEntry->save();
            }
        }

        return ['result' => 'ok'];
    }
}
