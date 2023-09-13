<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use simialbi\yii2\bulletin\models\Post;
use simialbi\yii2\bulletin\models\Topic;
use Throwable;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
                        'actions' => ['create'],
                        'roles' => ['bulletinAuthor']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['bulletinUpdatePost'],
                        'roleParams' => function () {
                            return ['topic' => $this->findModel(Yii::$app->request->get('id'))];
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['bulletinDeletePost'],
                        'roleParams' => function () {
                            return ['topic' => $this->findModel(Yii::$app->request->get('id'))];
                        }
                    ],
                ]
            ],
            'verbs' => [
                'class' => '\yii\filters\VerbFilter',
                'actions' => [
                    'delete' => ['POST', 'DELETE'],
                ]
            ]
        ];
    }

    /**
     * Create a new post in topic
     *
     * @param int $topicId The topic's id
     * @param int $boardId The current active board's id
     * @param int|null $postId The post to create a cite to
     *
     * @return string|Response
     *
     * @throws Exception
     */
    public function actionCreate(int $topicId, int $boardId, ?int $postId = null): Response|string
    {
        $topic = Topic::findOne($topicId);
        $post = new Post([
            'topic_id' => $topicId,
            'cite_id' => $postId,
            'title' => 'Re: ' . $topic->title
        ]);

        if ($post->load(Yii::$app->request->post()) && $post->save()) {
            $post->saveAttachments();

            $emails = [];
            foreach ($post->topic->boards as $board) {
                foreach ($board->users as $user) {
                    if ($user->email) {
                        $emails[] = $user->email;
                    }
                }
            }
            if (!empty($emails) && Yii::$app->mailer) {
                $emails = array_unique($emails);
                $from = ArrayHelper::getValue(Yii::$app->params, 'senderEmail', 'no-reply@' . Yii::$app->request->hostName);
                Yii::$app->mailer
                    ->compose([
                        'html' => '@simialbi/yii2/bulletin/mail/new-post-html',
                        'text' => '@simialbi/yii2/bulletin/mail/new-post-text'
                    ], ['topic' => $topic, 'post' => $post, 'boardId' => $boardId])
                    ->setFrom($from)
                    ->setTo($emails)
                    ->setSubject(Yii::t('simialbi/bulletin', 'New post created'))
                    ->send();
            }

            return $this->redirect(['topic/view', 'id' => $topicId, 'boardId' => $boardId]);
        }

        return $this->render('create', [
            'topic' => $topic,
            'model' => $post,
            'boardId' => $boardId,
            'rtfEditor' => $this->module->rtfEditor
        ]);
    }

    /**
     * Update an existing post
     *
     * @param int $id The post's id
     * @param int $boardId The active board's id
     *
     * @return string|Response
     *
     * @throws Exception|NotFoundHttpException|InvalidConfigException
     */
    public function actionUpdate(int $id, int $boardId): Response|string
    {
        $post = $this->findModel($id);
        $topic = $post->topic;

        if ($post->load(Yii::$app->request->post()) && $post->save()) {
            $post->saveAttachments();

            return $this->redirect(['topic/view', 'id' => $post->topic->id, 'boardId' => $boardId]);
        }

        return $this->render('update', [
            'topic' => $topic,
            'model' => $post,
            'boardId' => $boardId,
            'rtfEditor' => $this->module->rtfEditor
        ]);
    }

    /**
     * @param int $id The post's id
     * @param int $boardId The active board's id
     *
     * @return Response
     * @throws NotFoundHttpException|ErrorException|StaleObjectException|Throwable
     */
    public function actionDelete(int $id, int $boardId): Response
    {
        $model = $this->findModel($id);

        $path = FileHelper::normalizePath(Yii::getAlias("@webroot/web/uploads/bulletin-board/{$model->id}"));
        FileHelper::removeDirectory($path);

        $model->delete();

        return $this->redirect(['topic/view', 'id' => $model->topic_id, 'boardId' => $boardId]);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $condition
     *
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(mixed $condition): Post
    {
        if (($model = Post::findOne($condition)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}
