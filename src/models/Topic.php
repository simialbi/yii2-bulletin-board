<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\models;

use simialbi\yii2\models\UserInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $title
 * @property bool $has_voting
 * @property bool $status
 * @property int|string $created_by
 * @property int|string $updated_by
 * @property int|string|\DateTimeInterface $created_at
 * @property int|string|\DateTimeInterface $updated_at
 *
 * @property-read Board[] $boards
 * @property-read Category[] $categories
 * @property-read Post[] $posts
 * @property-read Voting $voting
 * @property-read UserInterface $author
 * @property-read UserInterface $updater
 */
class Topic extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%bulletin__topic}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            ['title', 'string'],
            [['status'], 'boolean'],

            [['status', 'has_voting'], 'default', 'value' => false],

            [['title', 'status'], 'required']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'blameable' => [
                'class' => '\yii\behaviors\BlameableBehavior',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => 'updated_by'
                ]
            ],
            'timestamp' => [
                'class' => '\yii\behaviors\TimestampBehavior',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at'
                ]
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('simialbi/bulletin/model/topic', 'Id'),
            'title' => Yii::t('simialbi/bulletin/model/topic', 'Title'),
            'has_voting' => Yii::t('simialbi/bulletin/model/topic', 'Has voting'),
            'status' => Yii::t('simialbi/bulletin/model/topic', 'Status'),
            'created_by' => Yii::t('simialbi/bulletin/model/topic', 'Created by'),
            'updated_by' => Yii::t('simialbi/bulletin/model/topic', 'Updated by'),
            'created_at' => Yii::t('simialbi/bulletin/model/topic', 'Created at'),
            'updated_at' => Yii::t('simialbi/bulletin/model/topic', 'Updated at')
        ];
    }

    /**
     * Get author
     * @return UserInterface
     */
    public function getAuthor(): UserInterface
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->created_by);
    }

    /**
     * Get updater
     * @return UserInterface
     */
    public function getUpdater(): UserInterface
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->updated_by);
    }

    /**
     * Get associated board
     * @return \yii\db\ActiveQuery
     * @throws InvalidConfigException
     */
    public function getBoards(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Board::class, ['id' => 'board_id'])
            ->viaTable('{{%bulletin__topic_board}}', ['topic_id' => 'id']);
    }

    /**
     * Get associated categories
     * @return \yii\db\ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategories(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('{{%bulletin__topic_category}}', ['topic_id' => 'id']);
    }

    /**
     * Get associated posts
     * @return \yii\db\ActiveQuery
     */
    public function getPosts(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Post::class, ['topic_id' => 'id']);
    }

    /**
     * Get associated voting
     * @return \yii\db\ActiveQuery
     */
    public function getVoting(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Voting::class, ['topic_id' => 'id']);
    }
}
