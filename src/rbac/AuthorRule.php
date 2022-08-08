<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\rbac;

use yii\rbac\Rule;

/**
 * Check if user ist the author of the specified post.
 */
class AuthorRule extends Rule
{
    /**
     * @inheritdoc
     */
    public $name = 'bulletin_isAuthor';

    /**
     * {@inheritDoc}
     */
    public function execute($user, $item, $params): bool
    {
        return isset($params['post']) && $params['post']->created_by == $user;
    }
}
