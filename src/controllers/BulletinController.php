<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\controllers;

use rmrevin\yii\fontawesome\FAS;
use simialbi\yii2\bulletin\models\Board;
use Yii;
use yii\base\InvalidConfigException;
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
     *
     * @throws InvalidConfigException
     */
    public function actionIndex(?int $id = null): string
    {
        $activeBoard = null;
        $navigation = self::getBoardNavigation($id, $activeBoard);

        $topicDataProvider = new ActiveDataProvider([
            'query' => $activeBoard ? $activeBoard->getTopics() : new Query(),
            'pagination' => [
                'pageSize' => 10
            ],
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

    /**
     * Get board navigation
     *
     * @param int|null $id The active board id
     * @param Board|null $activeBoard The active board instance
     *
     * @return array
     *
     * @throws \yii\base\InvalidConfigException
     */
    public static function getBoardNavigation(?int $id = null, ?Board &$activeBoard = null): array
    {
        $boards = Board::find()
            ->alias('b')
            ->leftJoin(['u' => '{{%bulletin__board_user}}'], '{{u}}.[[board_id]] = {{b}}.[[id]]')
            ->where([
                'or',
                ['is_public' => true],
                ['{{u}}.[[user_id]]' => Yii::$app->user->id]
            ])
            ->andWhere(['status' => true])
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

        return $navigation;
    }
}
