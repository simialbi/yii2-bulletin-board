<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use simialbi\yii2\bulletin\models\Board;
use simialbi\yii2\bulletin\models\Category;
use simialbi\yii2\bulletin\models\Post;
use simialbi\yii2\bulletin\models\Topic;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class TopicController extends Controller
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
//                        'actions' => ['create']
                    ]
                ]
            ]
        ];
    }

    /**
     * Create a new topic in board
     *
     * @param int $boardId The board where to create the topic
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate(int $boardId)
    {
        $topic = new Topic([
            'status' => true
        ]);
        $post = new Post();

        if ($topic->load(Yii::$app->request->post()) && $post->load(Yii::$app->request->post()) && $topic->save()) {
            $categories = Yii::$app->request->getBodyParam('categories');
            $boards = Yii::$app->request->getBodyParam('boards');

            foreach ($categories as $categoryId) {
                $category = Category::findOne($categoryId);
                $topic->link('categories', $category);
            }
            foreach ($boards as $bId) {
                $board = Board::findOne($bId);
                $topic->link('boards', $board);
            }

            $post->title = $topic->title;
            $post->status = $topic->status;
            $post->topic_id = $topic->id;
            $post->save();

            return $this->redirect(['view', 'id' => $topic->id, 'boardId' => $boardId]);
        }

        $categories = Category::find()
            ->select(['title', 'id'])
            ->indexBy('id')
            ->orderBy(['title' => SORT_ASC])
            ->column();
        $boards = Board::find()
            ->select(['title', 'id'])
            ->where(['status' => true])
            ->indexBy('id')
            ->orderBy(['title' => SORT_ASC])
            ->column();

        return $this->render('create', [
            'boardId' => $boardId,
            'boards' => $boards,
            'topic' => $topic,
            'post' => $post,
            'categories' => $categories
        ]);
    }

    /**
     * Display a topic with all posts
     *
     * @param int $id
     * @param int $boardId
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function actionView(int $id, int $boardId): string
    {
        $topic = Topic::find()
            ->where(['id' => $id])
            ->one();

        $postDataProvider = new ActiveDataProvider([
            'query' => $topic->getPosts()->with('topic')->with('topic.boards')->with('topic.categories'),
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_ASC
                ]
            ]
        ]);

        $navigation = [];
        $activeBoard = Board::findOne($boardId);
        if (!Yii::$app->request->isAjax) {
            $navigation = BulletinController::getBoardNavigation($boardId, $activeBoard);
        }

        return $this->render('view', [
            'topic' => $topic,
            'dataProvider' => $postDataProvider,
            'board' => $activeBoard,
            'navigation' => $navigation
        ]);
    }
}
