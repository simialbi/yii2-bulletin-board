<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use simialbi\yii2\bulletin\models\Post;
use simialbi\yii2\bulletin\models\Topic;
use Yii;
use yii\web\Controller;

class PostController extends Controller
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
     * Create a new post in topic
     *
     * @param int $topicId The topic's id
     * @param int $boardId The current active board's id
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate(int $topicId, int $boardId)
    {
        $topic = Topic::findOne($topicId);
        $post = new Post([
            'topic_id' => $topicId,
            'title' => 'Re: ' . $topic->title
        ]);

        if ($post->load(Yii::$app->request->post()) && $post->save()) {
            return $this->redirect(['topic/view', 'id' => $topicId, 'boardId' => $boardId]);
        }

        return $this->render('create', [
            'topic' => $topic,
            'model' => $post,
            'boardId' => $boardId
        ]);
    }
}
