<?php

namespace h3tech\crud\controllers;

use h3tech\crud\Module;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use h3tech\crud\models\Media;
use yii\helpers\FileHelper;
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

    protected static function getFileErrorMessage(int $error)
    {
        switch ($error) {
            case 1:
                return Yii::t('h3tech/crud/crud', 'The uploaded file exceeds the upload_max_filesize directive in php.ini');
            case 2:
                return Yii::t('h3tech/crud/crud', 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
            case 3:
                return Yii::t('h3tech/crud/crud', 'The uploaded file was only partially uploaded');
            case 4:
                return Yii::t('h3tech/crud/crud', 'No file was uploaded');
            case 6:
                return Yii::t('h3tech/crud/crud', 'Missing a temporary folder');
            case 7:
                return Yii::t('h3tech/crud/crud', 'Failed to write file to disk');
            case 8:
                return Yii::t('h3tech/crud/crud', 'A PHP extension stopped the file upload');
            default:
                throw new Exception(
                    Yii::t(
                        'h3tech/crud/crud',
                        'Unknown file error: {error}',
                        ['error' => $error]
                    )
                );
        }
    }

    public static function upload(UploadedFile $mediaFile, $type, $prefix)
    {
        if ($mediaFile->hasError) {
            throw new Exception(static::getFileErrorMessage($mediaFile->error));
        }

        $fileName = static::generateFileName($mediaFile->name, $type, $prefix);

        $mediaFile->saveAs(Media::getUploadPath($fileName));

        $media = new Media(['type' => $type, 'filename' => $fileName]);
        $media->save();

        return Yii::$app->db->lastInsertID;
    }

    public static function save($sourceFilePath, $type, $prefix)
    {
        $fileName = static::generateFileName(basename($sourceFilePath), $type, $prefix);

        copy($sourceFilePath, Media::getUploadPath($fileName));

        $media = new Media(['type' => $type, 'filename' => $fileName]);
        $media->save();

        return Yii::$app->db->lastInsertID;
    }

    protected static function generateFileName($originalName, $type, $prefix = null)
    {
        if ($prefix == null || trim($prefix) == '') {
            $prefix = $type . '_';
        }

        return uniqid($prefix) . '_' . static::slug($originalName);
    }

    protected static function slug($string, $replacement = '-', $lowercase = true)
    {
        $string = Inflector::transliterate($string);
        $string = preg_replace('/[^\.a-zA-Z0-9=\s—–-]+/u', '', $string);
        $string = preg_replace('/[=\s—–-]+/u', $replacement, $string);
        $string = trim($string, $replacement);

        return $lowercase ? strtolower($string) : $string;
    }

    /**
     * @param ActiveRecord $model
     * @param $mediaIdAttribute
     * @param $modelClass
     * @param $allowDeletion
     * @return array
     * @throws InvalidConfigException
     */
    public static function getSinglePreviewData($model, $mediaIdAttribute = null, $modelClass = null,
                                                $allowDeletion = true)
    {
        if ($modelClass === null) {
            $modelClass = get_class($model);
        }

        $result = [];
        $result['initialPreview'] = [];
        $result['initialPreviewConfig'] = [];

        $media = Media::findOne($model->$mediaIdAttribute);

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
                        'modelId' => $model->primaryKey,
                    ],
                    'downloadUrl' => $media->url,
                    'size' => $media->fileSize,
                    'fileId' => $media->primaryKey,
                ]);
            }

            $result['initialPreviewConfig'][] = $previewConfig;
        }

        return $result;
    }

    protected static function getMultiplePreviewConfig(ActiveRecord $model, Media $media, $junctionModelClass, $mediaIdAttribute, $modelIdAttribute)
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
                'modelIdAttribute' => $modelIdAttribute,
                'modelId' => $model->primaryKey,
            ],
            'downloadUrl' => $media->url,
            'size' => $media->fileSize,
            'fileId' => $media->primaryKey,
        ];
    }

    /**
     * @param ActiveRecord $model
     * @param ActiveRecord $junctionModelClass
     * @param string $modelIdAttribute
     * @param string $mediaIdAttribute
     * @param string $orderAttribute
     * @return array
     */
    public static function getMultiplePreviewData($model, $junctionModelClass, $modelIdAttribute, $mediaIdAttribute,
                                                  $orderAttribute = null)
    {
        $result = [];
        $result['initialPreview'] = [];
        $result['initialPreviewConfig'] = [];

        $junctionQuery = $junctionModelClass::find()->where([$modelIdAttribute => $model->primaryKey]);

        if ($orderAttribute !== null) {
            $junctionQuery->orderBy([$orderAttribute => SORT_ASC]);
        }
        $junctionEntries = $junctionQuery->all();

        foreach ($junctionEntries as $junctionEntry) {
            $media = Media::findOne($junctionEntry->$mediaIdAttribute);

            $result['initialPreview'][] = $media->url;
            $result['initialPreviewConfig'][] = static::getMultiplePreviewConfig(
                $model, $media, $junctionModelClass, $mediaIdAttribute, $modelIdAttribute
            );
        }

        return $result;
    }

    public function runAction($id, $params = [])
    {
        $params = array_merge($params, Yii::$app->request->post());
        return parent::runAction($id, $params);
    }

    /**
     * @param ActiveRecord $modelClass
     * @param ActiveRecord $junctionModelClass
     * @param string $type
     * @param string $modelIdAttribute
     * @param string $mediaIdAttribute
     * @param $modelId
     * @param string $modelName
     * @param string $prefix
     * @return array
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public static function actionUploadMultiple($modelClass, $junctionModelClass, $type, $modelIdAttribute, $mediaIdAttribute, $modelId,
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
                $model = $modelClass::findOne($modelId);

                $mediaId = static::upload($file, $type, $prefix);

                $junctionRecord = new $junctionModelClass;
                $junctionRecord->$modelIdAttribute = $modelId;
                $junctionRecord->$mediaIdAttribute = $mediaId;
                $junctionRecord->save();

                $media = Media::findOne($mediaId);

                $initialPreview = $media->url;
                $initialPreviewConfig = static::getMultiplePreviewConfig($model, $media, $junctionModelClass, $mediaIdAttribute, $modelIdAttribute);

                $response['initialPreview'] = [$initialPreview];
                $response['initialPreviewConfig'] = [$initialPreviewConfig];

                $response['result'] = 'ok';
            }
        } else {
            throw new BadRequestHttpException(Yii::t('h3tech/crud/crud', 'No uploaded file received'));
        }

        return $response;
    }

    protected function shouldDeleteOldMedia()
    {
        return Module::getInstance()->deleteOldMedia;
    }

    /**
     * @param ActiveRecord $modelClass
     * @param mixed $key
     * @param string $mediaIdAttribute
     * @param mixed $modelId
     * @return string[]
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDeleteSingle($modelClass, $key, $mediaIdAttribute, $modelId)
    {
        $modelClass::getDb()
            ->createCommand()
            ->update(
                $modelClass::tableName(), [$mediaIdAttribute => null], [$mediaIdAttribute => $key, 'id' => $modelId]
            )->execute();

        if ($this->shouldDeleteOldMedia() && $modelClass::find()->where([$mediaIdAttribute => $key])->count() == 0
            && ($media = Media::findOne($key)) !== null) {
            $media->delete();
        }

        return ['result' => 'ok'];
    }

    /**
     * @param ActiveRecord $junctionModelClass
     * @param $key
     * @param string $mediaIdAttribute
     * @param $modelId
     * @return string[]
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDeleteMultiple($junctionModelClass, $key, $mediaIdAttribute, $modelIdAttribute, $modelId)
    {
        $junctionModelClass::getDb()->createCommand()->delete($junctionModelClass::tableName(), [
            $mediaIdAttribute => $key,
            $modelIdAttribute => $modelId,
        ])->execute();

        if ($this->shouldDeleteOldMedia() && $junctionModelClass::find()->where([$mediaIdAttribute => $key])->count() == 0
            && ($media = Media::findOne($key)) !== null) {
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
