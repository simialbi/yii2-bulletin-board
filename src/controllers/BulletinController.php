<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use rmrevin\yii\fontawesome\FAS;
use simialbi\yii2\bulletin\models\Board;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\Controller;

class BulletinController extends Controller
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
                        'actions' => ['index']
                    ]
                ]
            ]
        ];
    }

    /**
     * Bulletin overview
     *
     * @param int|null $id
     *
     * @return string
     */
    public function actionIndex(?int $id = null): string
    {
        $boards = Board::find()
            ->alias('b')
            ->leftJoin(['u' => '{{%bulletin__board_user}}'], '{{u}}.[[board_id]] = {{b}}.[[id]]')
            ->where(['is_public' => true])
            ->orWhere(['{{u}}.[[user_id]]' => Yii::$app->user->id])
            ->orderBy(['title' => SORT_ASC])
            ->all();

        $navigation = [];
        foreach ($boards as $i => $board) {
            $active = false;
            if (is_null($id) && $i === 0) {
                $activeBoard = $board;
                $active = true;
            } elseif ($id === $board->id) {
                $activeBoard = $board;
                $active = true;
            }
            $label = $board->title;
            if ($board->icon) {
                $label = FAS::i($board->icon)->fixedWidth() . ' ' . $label;
            }
            $navigation[] = [
                'label' => $label,
                'url' => ['bulletin/index', 'id' => $board->id],
                'active' => $active
            ];
        }

        $topicDataProvider = new ActiveDataProvider([
            'query' => (isset($activeBoard)) ? $activeBoard->getTopics() : new Query(),
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]
        ]);

        return $this->render('index', [
            'navigation' => $navigation,
            'board' => $activeBoard ?? null,
            'dataProvider' => $topicDataProvider
        ]);
    }
}
