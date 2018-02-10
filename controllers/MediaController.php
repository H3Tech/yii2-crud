<?php

namespace h3tech\crud\controllers;

use h3tech\crud\Module;
use Yii;
use yii\base\InvalidParamException;
use yii\web\Controller;
use h3tech\crud\models\Media;
use yii\helpers\Html;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\UploadedFile;

class MediaController extends Controller {
    protected static function types() {
        return [
            'image' => [
                'defaultPrefix' => 'img_',
                'previewTemplate' => function($uploadPath, $filename) {
                    return Html::img($uploadPath.$filename,  ['class'=>'file-preview-image', 'style'=>'width: auto; max-height: 160px;', 'alt'=>$filename, 'title'=>$filename]);
                },
            ],
            'video' => [
                'defaultPrefix' => 'vid_',
                'previewTemplate' => function($uploadPath, $filename) {
                    return '<video height="160" controls><source src="'.$uploadPath.$filename.'"></video>';
                },
            ],
        ];
    }

    public static function upload(UploadedFile $mediaFile, $type, $prefix) {
        $types = self::types();
        if (!isset($types[$type])) {
            throw new InvalidParamException('Unknown media type: $type');
        }
        $currentType = $types[$type];

        if ($prefix == null || trim($prefix) == '') {
            $prefix = $currentType['defaultPrefix'];
        }
        $fileName = uniqid($prefix) . '_' . $mediaFile->name;

        $mediaFile->saveAs(Media::getUploadPath($fileName));

        $media = new Media();
        $media->type = $type;
        $media->filename = $fileName;
        $media->save();

        return Yii::$app->getDb()->getLastInsertID();
    }

    protected static function getPreviewTemplate($type, $filename) {
        $types = self::types();
        $currentType = $types[$type];
        $template = "";

        if ($currentType != null && $currentType['previewTemplate'] != null) {
            $baseUrl = Yii::$app->request->hostInfo.Yii::$app->request->baseUrl;
            $template = call_user_func($currentType['previewTemplate'], Module::getInstance()->relativeUploadPath, $filename);
        }

        return $template;
    }

    public static function getPreviewData($mediaId)
    {
        $result = [];
        $result['initialPreview'] = [];
        $result['initialPreviewConfig'] = [];

        $media = Media::findOne($mediaId);

        if ($media !== null) {
            $baseUrl = Yii::$app->request->hostInfo.Yii::$app->request->baseUrl;
            $result['initialPreview'][] = $media->uploadedUrl;
            $result['initialPreviewConfig'][] = ['caption' => $media->filename];
        }

        return $result;
    }

    public static function getMultiplePreviewData($modelId, $table, $mediaField, $modelField) {
        $result = array();
        $result['initialPreview'] = array();
        $result['initialPreviewConfig'] = array();

        $records = (new Query)
            ->select('*')->from($table)->where([$modelField => $modelId])
            ->createCommand()->queryAll();

        foreach ($records as $record) {
            $media = Media::findOne($record[$mediaField]);
            $mediaId = $media->getPrimaryKey();

            $initialPreview = self::getPreviewTemplate($media->type, $media->filename);

            $initialPreviewConfig = array(
                'caption' => $media->filename,
                'url' => Url::to(['/media/delete']),
                'key' => $mediaId,
                'extra' => [
                    'table' => $table,
                    'modelId' => $modelId,
                    'mediaId' => $mediaId,
                    'mediaField' => $mediaField,
                    'modelField' => $modelField,
                ],
            );

            array_push($result['initialPreview'], $initialPreview);
            array_push($result['initialPreviewConfig'], $initialPreviewConfig);
        }

        return $result;
    }

    public static function actionUpload() {
        $result = array();

        $postData = Yii::$app->request->post();

        $modelName = str_replace(' ', '', $postData['modelName']);
        $type = $postData['type'];
        $prefix = $postData['prefix'] == 'null' ? preg_replace('/\s/', '', strtolower($modelName)).'_' : $postData['prefix'];
        $table = $postData['table'];
        $mediaField = $postData['mediaField'];
        $modelField = $postData['modelField'];
        $modelId = $postData['modelId'];

        $files = UploadedFile::getInstancesByName($modelName);

        $mediaId = self::upload($files[0], $type, $prefix);

        Yii::$app->getDb()->createCommand()->
        insert($table, [
            $mediaField => $mediaId,
            $modelField => $modelId,
        ])->execute();

        $media = Media::findOne($mediaId);

        $initialPreview = self::getPreviewTemplate($media->type, $media->filename);

        $initialPreviewConfig = [
            'caption' => $media->filename,
            'url' => Url::to(['/media/delete']),
            'key' => $mediaId,
            'extra' => [
                'table' => $table,
                'modelId' => $modelId,
                'mediaId' => $mediaId,
                'mediaField' => $mediaField,
                'modelField' => $modelField,
            ],
        ];

        $result['initialPreview'] = array($initialPreview);
        $result['initialPreviewConfig'] = array($initialPreviewConfig);

        $result['result'] = 'ok';

        return json_encode($result);
    }

    public static function actionDelete() {
        $result = array();

        $postData = Yii::$app->request->post();

        $table = $postData['table'];
        $modelField = $postData['modelField'];
        $modelId = $postData['modelId'];
        $mediaField = $postData['mediaField'];
        $mediaId = $postData['mediaId'];

        Yii::$app->getDb()->createCommand()->
        delete($table, [
            $modelField => $modelId,
            $mediaField => $mediaId,
        ])->execute();

        $media = Media::findOne($mediaId);
        $media->delete();

        $result['result'] = 'ok';

        return json_encode($result);
    }
}
