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
use simialbi\yii2\bulletin\models\Voting;
use simialbi\yii2\bulletin\models\VotingAnswer;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
                        'actions' => ['create'],
                        'roles' => ['bulletinCreateTopic'],
                        'roleParams' => function () {
                            return ['board' => Board::findOne(Yii::$app->request->get('boardId'))];
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['bulletinUpdateTopic'],
                        'roleParams' => function () {
                            return ['topic' => $this->findModel(Yii::$app->request->get('id'))];
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['bulletinDeleteTopic'],
                        'roleParams' => function () {
                            return ['topic' => $this->findModel(Yii::$app->request->get('id'))];
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['bulletinAuthor']
                    ]
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
     * Create a new topic in board
     *
     * @param int $boardId The board where to create the topic
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate(int $boardId)
    {
        $topic = new Topic([
            'status' => true
        ]);
        $post = new Post();
        $voting = new Voting();
        $votingAnswer = new VotingAnswer();

        if ($topic->load(Yii::$app->request->post()) && $post->load(Yii::$app->request->post()) && $topic->save()) {
            $categories = Yii::$app->request->getBodyParam('categories', []);
            $boards = Yii::$app->request->getBodyParam('boards', []);

            foreach ($categories as $categoryId) {
                $category = Category::findOne($categoryId);
                $topic->link('categories', $category);
            }
            $emails = [];
            foreach ($boards as $bId) {
                $board = Board::findOne($bId);
                $topic->link('boards', $board);

                foreach ($board->users as $user) {
                    if ($user->email) {
                        $emails[] = $user->email;
                    }
                }
            }

            $post->title = $topic->title;
            $post->status = $topic->status;
            $post->topic_id = $topic->id;
            $post->save();
            $post->saveAttachments();

            if (!empty($emails) && Yii::$app->mailer) {
                $emails = array_unique($emails);
                $from = ArrayHelper::getValue(Yii::$app->params, 'senderEmail', 'no-reply@' . Yii::$app->request->hostName);
                Yii::$app->mailer
                    ->compose([
                        'html' => '@simialbi/yii2/bulletin/mail/new-topic-html',
                        'text' => '@simialbi/yii2/bulletin/mail/new-topic-text'
                    ], ['topic' => $topic, 'post' => $post, 'boardId' => $boardId])
                    ->setFrom($from)
                    ->setTo($emails)
                    ->setSubject(Yii::t('simialbi/bulletin', 'New topic created'))
                    ->send();
            }

            if ($topic->has_voting && $voting->load(Yii::$app->request->post())) {
                $voting->topic_id = $topic->id;
                if ($voting->save()) {
                    $answers = Yii::$app->request->getBodyParam($votingAnswer->formName());
                    foreach ($answers as $answer) {
                        $votingAnswer = new VotingAnswer();
                        $votingAnswer->load($answer, '');
                        $votingAnswer->voting_id = $voting->id;
                        $votingAnswer->save();
                    }
                }
            }

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
            'categories' => $categories,
            'voting' => $voting,
            'votingAnswer' => $votingAnswer
        ]);
    }

    /**
     * Update an existing topic
     *
     * @param int $id The topic id
     * @param int $boardId The active board id
     *
     * @return string|Response
     * @throws Exception|InvalidConfigException|NotFoundHttpException
     */
    public function actionUpdate(int $id, int $boardId)
    {
        $topic = $this->findModel($id);
        /** @var Post $post */
        $post = $topic->getPosts()->with('attachments')->orderBy(['id' => SORT_ASC])->one();
        $voting = ($topic->has_voting) ? $topic->voting : new Voting();
        $votingAnswer = new VotingAnswer();

        if ($topic->load(Yii::$app->request->post()) && $post->load(Yii::$app->request->post()) && $topic->save() && $post->save()) {
            $categories = Yii::$app->request->getBodyParam('categories', []);
            $boards = Yii::$app->request->getBodyParam('boards', []);

            $topic->unlinkAll('categories', true);
            foreach ($categories as $categoryId) {
                $category = Category::findOne($categoryId);
                $topic->link('categories', $category);
            }
            $topic->unlinkAll('boards', true);
            foreach ($boards as $bId) {
                $board = Board::findOne($bId);
                $topic->link('boards', $board);
            }

            $post->saveAttachments();

            if ($topic->has_voting && $voting->load(Yii::$app->request->post())) {
                if ($voting->save()) {
                    $voting->unlinkAll('answers', true);
                    $answers = Yii::$app->request->getBodyParam($votingAnswer->formName());
                    foreach ($answers as $answer) {
                        $votingAnswer = new VotingAnswer();
                        $votingAnswer->load($answer, '');
                        $votingAnswer->voting_id = $voting->id;
                        $votingAnswer->save();
                    }
                }
            }

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

        return $this->render('update', [
            'boardId' => $boardId,
            'boards' => $boards,
            'topic' => $topic,
            'post' => $post,
            'categories' => $categories,
            'voting' => $voting,
            'votingAnswer' => $votingAnswer
        ]);
    }

    /**
     * Display a topic with all posts
     *
     * @param int $id The topic id
     * @param int $boardId The active board id
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function actionView(int $id, int $boardId): string
    {
        $topic = Topic::find()
            ->with('voting')
            ->where(['id' => $id])
            ->one();

        $postDataProvider = new ActiveDataProvider([
            'query' => $topic->getPosts()->with('topic')->with('topic.boards')->with('topic.categories')->with('attachments'),
            'pagination' => [
                'pageSize' => 10
            ],
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

    /**
     * Delete a topic and all underlying posts and files
     *
     * @param int $id The topic id
     * @param int $boardId The active board id
     *
     * @return Response
     * @throws NotFoundHttpException|StaleObjectException|ErrorException
     */
    public function actionDelete(int $id, int $boardId): Response
    {
        $topic = $this->findModel($id);

        foreach ($topic->posts as $post) {
            $path = FileHelper::normalizePath(Yii::getAlias("@webroot/web/uploads/bulletin-board/{$post->id}"));
            FileHelper::removeDirectory($path);
        }

        $topic->delete();

        return $this->redirect(['bulletin/index', 'id' => $boardId]);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $condition
     *
     * @return Topic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($condition): Topic
    {
        if (($model = Topic::findOne($condition)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}
