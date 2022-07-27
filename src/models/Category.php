<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\models;

use simialbi\yii2\models\UserInterface;
use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $icon
 * @property bool $status
 * @property bool $is_public
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
            [['title', 'description', 'icon'], 'string'],
            [['status', 'is_public'], 'boolean'],

            [['status', 'is_public'], 'default', 'value' => true],

            [['title', 'status', 'is_public'], 'required']
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
            'icon' => Yii::t('simialbi/bulletin/model/category', 'Icon'),
            'status' => Yii::t('simialbi/bulletin/model/category', 'Status'),
            'is_public' => Yii::t('simialbi/bulletin/model/category', 'Is public'),
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
     * Get users associated with this category
     * @return UserInterface[]
     */
    public function getUsers(): array
    {
        if (!isset($this->_users)) {
            $query = new Query();
            $query->select('user_id')
                ->from('{{%bulletin__category_user}}')
                ->where(['category_id' => $this->id]);

            if (!$query->count('user_id')) {
                $this->_users = [];

                return $this->_users;
            }

            /** @var UserInterface[] $users */
            $this->_users = call_user_func([Yii::$app->user->identityClass, 'findIdentitiesByIds'], array_values($query->all()));
        }

        return $this->_users;
    }

    /**
     * Set the users for this category.
     * @param UserInterface[]|int[] $users The user ids or user instances to link.
     * @return int Number of inserted records
     * @throws \yii\db\Exception|InvalidCallException
     */
    public function setUsers(array $users): int
    {
        if ($this->isNewRecord) {
            throw new InvalidCallException('Unable to link models: the models being linked cannot be newly created.');
        }
        $ids = [];
        foreach ($users as $user) {
            if ($user instanceof UserInterface) {
                $ids[] = [$user->getId(), $this->id];
            } elseif (is_int($user)) {
                $ids[] = [$user, $this->id];
            }
        }

        return self::getDb()
            ->createCommand()
            ->batchInsert('{{%bulletin__category_user}}', ['user_id', 'category_id'], $ids)
            ->execute();
    }

    /**
     * Get associated topics
     * @return \yii\db\ActiveQuery
     */
    public function getTopics(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Topic::class, ['category_id' => 'id']);
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
