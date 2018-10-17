<?php

namespace h3tech\crud\controllers;

use h3tech\crud\controllers\actions\Action;
use h3tech\crud\formatters\CrudFormatter;
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
use h3tech\crud\helpers\CrudWidget;
use yii\helpers\Html;

/**
 * This class implements the CRUD actions for a model.
 */
abstract class AbstractCRUDController extends Controller
{
    protected static $modelClass = null;
    protected static $searchModelClass = null;
    protected static $pageSize = 20;
    protected static $enableAjaxValidation = false;
    protected static $enableClientValidation = true;
    protected static $titleAttribute = null;
    protected static $indexAttributes = null;
    protected static $modelNameLabel = null;
    protected static $viewAttributes = null;
    protected static $supportedActions = null;
    protected static $allowedActions = null;
    protected static $disallowedActions = null;
    protected static $itemButtons = null;
    protected static $itemActions = null;
    protected static $detailFormatterClass = null;
    protected static $afterActionRedirects = ['create' => 'view', 'update' => 'view', 'delete' => 'index'];
    protected static $showFilterResetButton = null;

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

    protected static function getEnableClientValidation()
    {
        return static::$enableClientValidation;
    }

    public static function titleAttribute()
    {
        return static::$titleAttribute === null ? 'id' : static::$titleAttribute;
    }

    public static function showFilterResetButton()
    {
        return static::$showFilterResetButton === null ? false : static::$showFilterResetButton;
    }

    public static function modelName()
    {
        $modelClass = static::modelClass();
        $model = new $modelClass();
        $reflection = new \ReflectionClass($model);
        // Generate model name from database table name
        //return preg_replace(['/_/', '/\b(\w)/e'], [' ', '" ".strtoupper("$1")'], $modelClass::tableName());
        // Generate model name from actual class name
        return preg_replace('/([a-z])([A-Z]+)/', '$1 $2', $reflection->getShortName());
    }

    public static function modelNameLabel()
    {
        if (static::$modelNameLabel !== null) {
            return static::$modelNameLabel;
        }

        return static::modelName();
    }

    public static function getModelPrefix()
    {
        return preg_replace('/\s/', '', strtolower(static::modelName())) . '_';
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
            'modelName' => static::modelName(),
            'modelNameLabel' => static::modelNameLabel(),
            'controllerClass' => get_class($this),
            'viewPaths' => $this->getViewPaths(),
            'relativeViewPaths' => $this->getRelativeViewPaths(),
            'enableAjaxValidation' => static::getEnableAjaxValidation(),
            'enableClientValidation' => static::getEnableClientValidation(),
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
        if (static::$indexAttributes !== null) {
            return static::$indexAttributes;
        }

        $allAttributes = static::modelAttributes();
        return array_splice($allAttributes, 0, 5);
    }

    public static function viewAttributes(ActiveRecord $model)
    {
        if (static::$viewAttributes !== null) {
            return static::$viewAttributes;
        }

        return $model->attributes();
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

    public static function formRules(ActiveRecord $model)
    {
        $formRules = [];

        $attributes = array_diff($model->attributes(), $model::getTableSchema()->primaryKey);
        foreach ($attributes as $attribute) {
            $formRules[$attribute] = ['textInput'];
        }

        return $formRules;
    }

    protected static function actionRules()
    {
        return [];
    }

    public static function supportedActions()
    {
        if (static::$supportedActions !== null) {
            return static::$supportedActions;
        }

        return ['index', 'create', 'view', 'update', 'delete'];
    }

    public static function itemActions()
    {
        if (static::$itemActions !== null) {
            return static::$itemActions;
        }

        return ['view', 'update', 'delete'];
    }

    public static function allowedActions()
    {
        $allowedActions = [];

        if (static::$allowedActions === null) {
            $allowedActions = static::supportedActions();
        } else {
            $allowedActions = static::$allowedActions;
        }

        if (static::$disallowedActions !== null) {
            $allowedActions = array_diff($allowedActions, static::$disallowedActions);
        }

        return $allowedActions;
    }

    protected static function disallowedActions()
    {
        return array_diff(static::supportedActions(), static::allowedActions());
    }

    public static function isActionAllowed($action)
    {
        return in_array($action, static::allowedActions());
    }

    public function behaviors()
    {
        $accessRules = [];

        if (!empty($disallowedActions = static::disallowedActions())) {
            $accessRules[] = [
                'allow' => false,
                'actions' => $disallowedActions,
                'roles' => ['@'],
            ];
        }

        $accessRules[] = [
            'allow' => true,
            'roles' => ['@'],
        ];

        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => $accessRules,
            ],
        ];
    }

    public static function itemButtons()
    {
        if (static::$itemButtons !== null) {
            return static::$itemButtons;
        }

        return ['view', 'update', 'delete'];
    }

    public static function detailFormatterClass()
    {
        if (static::$detailFormatterClass !== null) {
            return static::$detailFormatterClass;
        }

        return CrudFormatter::class;
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

    protected function transformModel(ActiveRecord $model, $actionType, $before = false)
    {
        foreach (static::actionRules() as $rule) {
            if (is_string($rule)) {
                $rule = ['class' => $rule];
            }

            /** @var Action $action */
            $action = Yii::createObject(array_merge($rule, [
                'controllerClass' => get_class($this),
            ]));
            call_user_func([$action, ($before ? 'before' : 'after') . ucfirst($actionType)], $model);
        }
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
        $searchParams = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($searchParams);
        static::transformDataProvider($dataProvider);
        $dataProvider->pagination->setPageSize(static::pageSize());

        return $this->renderAction('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    protected static function transformDataProvider(ActiveDataProvider $dataProvider)
    {
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

        if ($this->canValidateModel($model)) {
            $this->transformModel($model, $action, true);
        }

        if ($this->canCreateModel($model)) {
            $this->transformModel($model, $action);
            $model->save();
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

        if ($this->canValidateModel()) {
            $this->transformModel($model, $action, true);
        }

        if ($this->canUpdateModel($model)) {
            $this->transformModel($model, $action);
            $model->save();
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
        $this->deleteModel(static::findModel($id));
        return $this->redirect([static::afterActionRedirects()['delete']]);
    }

    protected function deleteModel(ActiveRecord $model)
    {
        $action = 'delete';

        $this->transformModel($model, $action, true);
        $model->delete();
        $this->transformModel($model, $action);
    }

    public static function afterActionRedirects()
    {
        return static::$afterActionRedirects;
    }

    protected static function datePickerFilterDefinition($attribute)
    {
        return CrudWidget::datePickerFilterDefinition(static::searchModelClass(), $attribute);
    }

    public static function gridConfig()
    {
        return [];
    }

    public static function detailButtons(ActiveRecord $model)
    {
        $buttons = [];

        if (static::isActionAllowed('update')) {
            $buttons['update'] = Html::a(
                Yii::t('h3tech/crud/crud', 'Update'),
                ['update', 'id' => $model->primaryKey],
                ['class' => 'btn btn-primary']
            );
        }

        if (static::isActionAllowed('delete')) {
            $buttons['delete'] = Html::a(Yii::t('h3tech/crud/crud', 'Delete'), ['delete', 'id' => $model->primaryKey], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('h3tech/crud/crud', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]);
        }

        return $buttons;
    }
}
