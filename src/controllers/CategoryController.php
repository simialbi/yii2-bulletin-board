<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use simialbi\yii2\bulletin\models\Category;
use simialbi\yii2\bulletin\models\SearchCategory;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CategoryController extends Controller
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
                        'roles' => ['bulletinCreateCategory']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['bulletinUpdateCategory']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['bulletinDeleteCategory']
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
        $searchModel = new SearchCategory();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Create a new category
     *
     * @return string|Response
     */
    public function actionCreate(): Response|string
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Update an existing category by id
     *
     * @param int $id The category's primary key
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Delete category
     *
     * @param integer $id  The category's primary key
     *
     * @return Response
     * @throws NotFoundHttpException|StaleObjectException|Throwable
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect('index');
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $condition
     *
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(mixed $condition): Category
    {
        if (($model = Category::findOne($condition)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}
