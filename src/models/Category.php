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
 * @property string|null $description
 * @property int|string $created_by
 * @property int|string $updated_by
 * @property int|string|\DateTimeInterface $created_at
 * @property int|string|\DateTimeInterface $updated_at
 *
 * @property UserInterface[] $users
 * @property-read Topic[] $topics
 * @property-read Post[] $posts
 * @property-read UserInterface $author
 * @property-read UserInterface $updater
 */
class Category extends ActiveRecord
{
    private array $_users;

    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%bulletin__category}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            [['title', 'description'], 'string'],

            [['title'], 'required']
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
            'id' => Yii::t('simialbi/bulletin/model/category', 'Id'),
            'title' => Yii::t('simialbi/bulletin/model/category', 'Title'),
            'description' => Yii::t('simialbi/bulletin/model/category', 'Description'),
            'created_by' => Yii::t('simialbi/bulletin/model/category', 'Created by'),
            'updated_by' => Yii::t('simialbi/bulletin/model/category', 'Updated by'),
            'created_at' => Yii::t('simialbi/bulletin/model/category', 'Created at'),
            'updated_at' => Yii::t('simialbi/bulletin/model/category', 'Updated at')
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
     * Get associated topics
     * @return \yii\db\ActiveQuery
     * @throws InvalidConfigException
     */
    public function getTopics(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Topic::class, ['id' => 'topic_id'])
            ->viaTable('{{%bulletin__topic_category}}', ['category_id' => 'id']);
    }

    /**
     * Get associated posts via topics
     * @return \yii\db\ActiveQuery
     */
    public function getPosts(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Post::class, ['topic_id' => 'id'])
            ->via('topic');
    }
}
