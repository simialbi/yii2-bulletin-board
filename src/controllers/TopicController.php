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

    public function actionCreate(int $boardId)
    {
        $topic = new Topic([
            'board_id' => $boardId
        ]);
        $post = new Post();

        if ($topic->load(Yii::$app->request->post()) && $post->load(Yii::$app->request->post()) && $topic->save()) {
            $post->topic_id = $topic->id;
            $post->save();

            return $this->redirect(['index', 'id' => $topic->id]);
        }

        return $this->render('create', [
            'topic' => $topic,
            'post' => $post
        ]);
    }
}
