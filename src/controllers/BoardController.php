<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use rmrevin\yii\fontawesome\FAS;
use simialbi\yii2\bulletin\models\Board;
use simialbi\yii2\bulletin\models\SearchBoard;
use Yii;
use yii\caching\FileDependency;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

class BoardController extends \yii\web\Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => '\yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['bulletinAdministrator']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['bulletinCreateBoard']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['bulletinUpdateBoard']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['bulletinDeleteBoard']
                    ]
                ]
            ]
        ];
    }

    /**
     * Show all categories
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new SearchBoard();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'users' => ArrayHelper::map(call_user_func([
                Yii::$app->user->identityClass,
                'findIdentities'
            ]), 'id', 'name')
        ]);
    }

    /**
     * Create a new board
     *
     * @return string|\yii\web\Response
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new Board();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (!$model->is_public) {
                $users = Yii::$app->request->getBodyParam('authorized-users', []);
                $users[] = Yii::$app->user->id;
                $users = array_unique($users);

                $model->setUsers($users);
            }

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'icons' => $this->getIcons(),
            'users' => ArrayHelper::map(call_user_func([
                Yii::$app->user->identityClass,
                'findIdentities'
            ]), 'id', 'name')
        ]);
    }

    /**
     * Update an existing board
     *
     * @param int $id The board's primary key
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (!$model->is_public) {
                $users = Yii::$app->request->getBodyParam('authorized-users', []);
                $users[] = Yii::$app->user->id;
                $users = array_unique($users);

                $model->setUsers($users);
            } else {
                $model->setUsers([]);
            }

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'icons' => $this->getIcons(),
            'users' => ArrayHelper::map(call_user_func([
                Yii::$app->user->identityClass,
                'findIdentities'
            ]), 'id', 'name')
        ]);
    }

    /**
     * Delete board
     *
     * @param integer $id The boards's primary key
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException|\yii\db\StaleObjectException
     */
    public function actionDelete(int $id): \yii\web\Response
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Get the an array of fontawesome icons
     *
     * @return array
     */
    private function getIcons(): array
    {
        if (!function_exists('yaml_parse')) {
            return [];
        }
        $icons = [];
        if (is_dir($dir = Yii::getAlias('@vendor/fortawesome/font-awesome-pro-6'))) {
        } elseif (is_dir($dir = Yii::getAlias('@vendor/fortawesome/font-awesome-pro'))) {
        } elseif (is_dir($dir = Yii::getAlias('@vendor/fortawesome/font-awesome'))) {
        } else {
            $dir = false;
        }

        if ($dir && file_exists($path = FileHelper::normalizePath($dir . '/metadata/icons.yml'))) {
            $dependency = new FileDependency();
            $dependency->fileName = $path;
            $icons = Yii::$app->cache->getOrSet('sa-bulletin-icons', function () use ($path) {
                $icons = [];
                $data = yaml_parse(file_get_contents($path));
                foreach ($data as $icon => $value) {
                    if ($icon != '0' && $value['styles'][0] !== 'brands') {
                        $icons[$icon] = (string)FAS::i($icon);
                    }
                }
                return $icons;
            }, 0, $dependency);
        }

        return $icons;
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $condition
     *
     * @return Board the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($condition): Board
    {
        if (($model = Board::findOne($condition)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}
