<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use simialbi\yii2\bulletin\models\Board;
use simialbi\yii2\bulletin\models\Voting;
use simialbi\yii2\bulletin\models\VotingUserAnswer;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class VotingController extends Controller
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
                        'actions' => ['view', 'vote', 'chart-data'],
                        'roles' => ['bulletinAuthor']
                    ]
                ]
            ]
        ];
    }

    /**
     * Show a voting
     *
     * @param int $id
     * @param int $boardId
     * @param bool $results
     * @return string
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionView(int $id, int $boardId, bool $results = false): string
    {
        $model = $this->findModel($id);

        $navigation = [];
        $activeBoard = Board::findOne($boardId);
        if (!Yii::$app->request->isAjax) {
            $navigation = BulletinController::getBoardNavigation($boardId, $activeBoard);
        }

        $userHasVoted = VotingUserAnswer::find()
            ->alias('ua')
            ->innerJoinWith('voting v')
            ->where([
                '{{ua}}.[[user_id]]' => Yii::$app->user->id,
                '{{v}}.[[id]]' => $id
            ])
            ->exists();

        $userAnswer = new VotingUserAnswer();

        return $this->render('view', [
            'model' => $model,
            'navigation' => $navigation,
            'board' => $activeBoard,
            'userHasVoted' => $userHasVoted,
            'userAnswer' => $userAnswer,
            'results' => $results
        ]);
    }

    /**
     * @param int $id
     * @param int $boardId
     *
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionVote(int $id, int $boardId): Response
    {
        $userAnswer = new VotingUserAnswer();

        $answers = Yii::$app->request->getBodyParam($userAnswer->formName());
        $answers['answer_id'] = (array)$answers['answer_id'];
        foreach ($answers['answer_id'] as $answer) {
            $userAnswer = new VotingUserAnswer();
            $userAnswer->answer_id = $answer;
            $userAnswer->user_id = (string)Yii::$app->user->id;
            $userAnswer->save();
        }

        return $this->redirect(['voting/view', 'id' => $id, 'boardId' => $boardId]);
    }

    /**
     * Get chart data for specific voting
     *
     * @param int $id
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionChartData(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $data = [];

        foreach ($model->answers as $answer) {
            $data[$answer->id] = [
                'answer' => $answer->answer,
                'count' => 0,
                'users' => ''
            ];
        }
        foreach ($model->userAnswers as $userAnswer) {
            $data[$userAnswer->answer_id]['count']++;
            $data[$userAnswer->answer_id]['users'] .= ($data[$userAnswer->answer_id]['users'] === '')
                ? $userAnswer->user->name
                : ', ' . $userAnswer->user->name;
        }

        return array_values($data);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $condition
     *
     * @return Voting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(mixed $condition): Voting
    {
        if (($model = Voting::findOne($condition)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}
