<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\rbac;

use simialbi\yii2\bulletin\models\Board;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Rule;

class RelatedRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'isRelated';

    /**
     * {@inheritDoc}
     */
    public function execute($user, $item, $params): bool
    {
        $board = Board::findOne(Yii::$app->request->get('boardId'));
        if ($board && ($board->is_public || in_array($user, ArrayHelper::getColumn($board->users, 'id')))) {
            return true;
        }

        return false;
    }
}
