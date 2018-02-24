<?php

namespace h3tech\crud\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use h3tech\crud\models\Media;
use yii\db\Query;
use yii\base\ViewNotFoundException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\base\Model;

/**
 * This class implements the CRUD actions for a model.
 */
abstract class AbstractCRUDController extends Controller
{
    protected static $modelClass = null;
    protected static $searchModelClass = null;
    protected static $pageSize = 20;
    protected static $enableAjaxValidation = false;

    protected static function modelClass()
    {
        return static::$modelClass === null
            ? ('app\models\\' . static::shortName())
            : static::$modelClass;
    }

    protected static function searchModelClass()
    {
        return static::$searchModelClass === null
            ? ('app\controllers\search\\' . static::shortName() . 'Search')
            : static::$searchModelClass;
    }

    protected static function pageSize()
    {
        return static::$pageSize;
    }

    protected static function getEnableAjaxValidation()
    {
        return static::$enableAjaxValidation;
    }

    public static function getModelName()
    {
        $modelClass = static::modelClass();
        $model = new $modelClass();
        $reflection = new \ReflectionClass($model);
        // Generate model name from database table name
        //return preg_replace(['/_/', '/\b(\w)/e'], [' ', '" ".strtoupper("$1")'], $modelClass::tableName());
        // Generate model name from actual class name
        return preg_replace('/([a-z])([A-Z]+)/', '$1 $2', $reflection->getShortName());
    }

    public static function getModelPrefix()
    {
        return preg_replace('/\s/', '', strtolower(static::getModelName())) . '_';
    }

    protected function getViewPaths()
    {
        return [
            $this->viewPath,
            $this->module->viewPath,
            $this->getDefaultViewPath(),
        ];
    }

    protected function commonViewData()
    {
        return [
            'modelClass' => static::modelClass(),
            'modelName' => static::getModelName(),
            'controllerClass' => get_class($this),
            'viewPaths' => $this->getViewPaths(),
            'relativeViewPaths' => $this->getRelativeViewPaths(),
            'enableAjaxValidation' => static::getEnableAjaxValidation(),
        ];
    }

    protected static function modelAttributes()
    {
        $modelClass = static::modelClass();
        /** @var ActiveRecord $model */
        $model = new $modelClass();
        return $model->attributes();
    }

    public static function indexAttributes()
    {
        $allAttributes = static::modelAttributes();
        return array_splice($allAttributes, 0, 5);
    }

    protected static function shortName()
    {
        $reflection = new \ReflectionClass(get_called_class());
        $className = $reflection->getShortName();
        return substr($className, 0, strrpos($className, 'Controller'));
    }

    protected static function primaryFields()
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = static::modelClass();

        return $modelClass::getTableSchema()->primaryKey;
    }

    public static function viewRules()
    {
        $viewRules = [];

        $attributes = array_diff(static::modelAttributes(), static::primaryFields());
        foreach ($attributes as $attribute) {
            $viewRules[$attribute] = ['textInput'];
        }

        return $viewRules;
    }

    protected static function actionRules()
    {
        return [];
    }

    protected static function fieldActions()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return [
            'media' => [
                'createFunction' => function (ActiveRecord $model, $type, $mediaField, $fileVar, $prefix = null) {
                    $mediaFile = UploadedFile::getInstance($model, $fileVar);
                    if ($mediaFile !== null) {
                        $model[$mediaField] = MediaController::upload($mediaFile, $type, ($prefix == null ? static::getModelPrefix() : $prefix));
                    }
                },
                'updateFunction' => function (ActiveRecord $model, $type, $mediaField, $fileVar, $prefix = null) {
                    $mediaFile = UploadedFile::getInstance($model, $fileVar);
                    if ($mediaFile !== null) {
                        $oldMedia = Media::findOne($model[$mediaField]);
                        if ($oldMedia !== null) {
                            $oldMedia->delete();
                        }
                        $model[$mediaField] = MediaController::upload($mediaFile, $type, ($prefix == null ? static::getModelPrefix() : $prefix));
                    }
                },
                'deleteFunction' => function (ActiveRecord $model, $type, $mediaField) {
                    $media = Media::findOne($model[$mediaField]);
                    if ($media !== null) {
                        $media->delete();
                    }
                },
            ],
            'media_multiple' => [
                'createFunction' => function (ActiveRecord $model, $type, $tableName, $mediaField, $modelField, $fileVar, $prefix = null) {
                    $mediaFiles = UploadedFile::getInstances($model, $fileVar);
                    if ($mediaFiles) {
                        foreach ($mediaFiles as $mediaFile) {
                            $mediaId = MediaController::upload($mediaFile, $type, ($prefix == null ? static::getModelPrefix() : $prefix));
                            Yii::$app->getDb()->createCommand()->
                            insert($tableName, [
                                $mediaField => $mediaId,
                                $modelField => $model->getPrimaryKey(),
                            ])->execute();
                        }
                    }
                },
                'deleteFunction' => function (ActiveRecord $model, $type, $tableName, $mediaField, $modelField) {
                    $identity = [
                        $modelField => $model->getPrimaryKey()
                    ];

                    $mediaInstances = (new Query)
                        ->select('*')->from($tableName)->where($identity)
                        ->createCommand()->queryAll();

                    foreach ($mediaInstances as $media) {
                        Media::findOne($media[$mediaField])->delete();
                    }

                    Yii::$app->getDb()->createCommand()->
                    delete($tableName, $identity)->execute();
                },
            ],
            'junction' => [
                'createFunction' => function (ActiveRecord $model, $junctionModel, $localField, $remoteField, $modelVariable) {
                    foreach ($model->$modelVariable as $foreignKey) {
                        $identity = [
                            $localField => $model->primaryKey,
                            $remoteField => $foreignKey,
                        ];

                        /** @var ActiveRecord $junctionModel */
                        /** @var ActiveRecord $junctionEntry */
                        if (($junctionEntry = $junctionModel::find()->where($identity)->one()) === null) {
                            $junctionEntry = new $junctionModel();
                            $junctionEntry->load($identity, '');
                            $junctionEntry->save();
                        }
                    }
                },
                'updateFunction' => function (ActiveRecord $model, $junctionModel, $localField, $remoteField, $modelVariable) {
                    $foreignKeys = $model->$modelVariable;

                    /** @var ActiveRecord $junctionModel */
                    if (empty($foreignKeys)) {
                        $junctionModel::deleteAll([$localField => $model->primaryKey]);
                    } else {
                        $junctionModel::deleteAll([
                            'and',
                            [$localField => $model->primaryKey],
                            ['not in', $remoteField, $foreignKeys],
                        ]);
                    }

                    foreach ($foreignKeys as $foreignKey) {
                        $identity = [
                            $localField => $model->primaryKey,
                            $remoteField => $foreignKey,
                        ];

                        /** @var ActiveRecord $junctionEntry */
                        if (($junctionEntry = $junctionModel::find()->where($identity)->one()) === null) {
                            $junctionEntry = new $junctionModel();
                            $junctionEntry->load($identity, '');
                            $junctionEntry->save();
                        }
                    }
                },
                'deleteFunction' => function (ActiveRecord $model, $junctionModel, $localField) {
                    /** @var ActiveRecord $junctionModel */
                    $junctionModel::deleteAll([$localField => $model->primaryKey]);
                },
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    protected static function getMediaInstances(ActiveRecord $model, $tableName, $mediaField, $modelField)
    {
        $result = array();

        $mediaInstances = (new Query)
            ->select('*')->from($tableName)->where([$modelField => $model->getPrimaryKey()])
            ->createCommand()->queryAll();

        foreach ($mediaInstances as $instance) {
            array_push($result, Media::findOne($instance[$mediaField]));
        }

        return $result;
    }

    public static function getDefaultViewPath($isAbsolute = true)
    {
        $path = '@h3tech/crud/views';
        return $isAbsolute ? Yii::getAlias($path) : $path;
    }

    protected function processData(ActiveRecord $model, $actionType)
    {
        $actions = static::fieldActions();

        foreach (static::actionRules() as $rule) {
            $ruleName = $rule[0];
            $action = $actions[$ruleName];

            if ($action != null) {
                $function = isset($action[$actionType . 'Function']) ? $action[$actionType . 'Function'] : null;
                if (is_callable($function)) {
                    $rule[0] = $model;
                    call_user_func_array($function, $rule);
                }
            } else {
                die("Unknown field action '$ruleName'!");
            }
        }

        $model->save();
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected static function findModel($id)
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = static::modelClass();
        if (($model = $modelClass::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected static function getRelativeViewPaths($action = '')
    {
        return [
            $action,
            "/$action",
            "@h3tech/crud/views/$action",
        ];
    }

    protected function renderAction($action, $params = [])
    {
        $finalParameters = array_merge($this->commonViewData(), $params);
        $finalParameters = array_merge($finalParameters, ['renderParams' => $finalParameters]);

        foreach ($this->getRelativeViewPaths($action) as $viewPath) {
            try {
                return $this->render($viewPath, $finalParameters);
            } catch (ViewNotFoundException $e) {
                continue;
            }
        }

        return null;
    }

    protected function renderJson(array $json)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $json;
    }

    protected function canValidateModel(ActiveRecord $model)
    {
        return $model->load(Yii::$app->request->post());
    }

    protected function shouldAjaxValidate(ActiveRecord $model)
    {
        return Yii::$app->request->isAjax && $this->canValidateModel($model);
    }

    protected function ajaxValidateModel(ActiveRecord $model)
    {
        return ActiveForm::validate($model);
    }

    /**
     * Lists all models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModelClass = static::searchModelClass();
        $searchModel = new $searchModelClass();
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->setPageSize(static::pageSize());

        return $this->renderAction('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    protected function canCreateModel(ActiveRecord $model)
    {
        return $this->canValidateModel($model) && $model->save();
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $action = 'create';

        /** @var ActiveRecord $model */
        $modelClass = static::modelClass();
        $model = new $modelClass();

        if ($this->shouldAjaxValidate($model)) {
            return $this->renderJson($this->ajaxValidateModel($model));
        }

        if ($this->canCreateModel($model)) {
            $this->processData($model, $action);
            return $this->redirect([static::afterActionRedirects()[$action], 'id' => $model->getPrimaryKey()]);
        } else {
            return $this->renderAction($action, ['model' => $model]);
        }
    }

    /**
     * Displays a single model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->renderAction('view', ['model' => static::findModel($id)]);
    }

    protected function canUpdateModel(ActiveRecord $model)
    {
        return $this->canValidateModel($model) && $model->validate();
    }

    /**
     * Updates an existing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $action = 'update';

        $model = static::findModel($id);

        if ($this->shouldAjaxValidate($model)) {
            return $this->renderJson($this->ajaxValidateModel($model));
        }

        if ($this->canUpdateModel($model)) {
            $this->processData($model, $action);
            return $this->redirect([static::afterActionRedirects()[$action], 'id' => $model->getPrimaryKey()]);
        } else {
            return $this->renderAction($action, ['model' => $model]);
        }
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $action = 'delete';

        $model = static::findModel($id);
        $this->processData($model, $action);
        $model->delete();

        return $this->redirect([static::afterActionRedirects()[$action]]);
    }

    public static function afterActionRedirects()
    {
        return [
            'create' => 'view',
            'update' => 'view',
            'delete' => 'index',
        ];
    }
}
