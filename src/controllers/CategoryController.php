<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use simialbi\yii2\bulletin\models\Category;
use simialbi\yii2\bulletin\models\SearchCategory;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
     * @return string|\yii\web\Response
     */
    public function actionCreate()
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
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id)
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
     * @return \yii\web\Response
     * @throws NotFoundHttpException|\yii\db\StaleObjectException
     */
    public function actionDelete(int $id): \yii\web\Response
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
    protected function findModel($condition): Category
    {
        if (($model = Category::findOne($condition)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}
