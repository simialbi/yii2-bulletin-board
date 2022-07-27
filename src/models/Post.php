<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\models;

use simialbi\yii2\models\UserInterface;
use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $topic_id
 * @property string $title
 * @property string $text
 * @property bool $status
 * @property int|string $created_by
 * @property int|string $updated_by
 * @property int|string|\DateTimeInterface $created_at
 * @property int|string|\DateTimeInterface $updated_at
 *
 * @property-read Topic $topic
 * @property-read Category $category
 * @property-read Attachment[] $attachments
 * @property-read UserInterface $author
 * @property-read UserInterface $updater
 */
class Post extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%bulletin__post}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'topic_id'], 'integer'],
            [['title', 'text'], 'string'],
            ['status', 'boolean'],

            ['status', 'default', 'value' => true],

            [['topic_id', 'title', 'text', 'status'], 'required']
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
            'id' => Yii::t('simialbi/bulletin/model/post', 'Id'),
            'topic_id' => Yii::t('simialbi/bulletin/model/post', 'Topic'),
            'title' => Yii::t('simialbi/bulletin/model/post', 'Title'),
            'text' => Yii::t('simialbi/bulletin/model/post', 'Text'),
            'status' => Yii::t('simialbi/bulletin/model/post', 'Status'),
            'created_by' => Yii::t('simialbi/bulletin/model/post', 'Created by'),
            'updated_by' => Yii::t('simialbi/bulletin/model/post', 'Updated by'),
            'created_at' => Yii::t('simialbi/bulletin/model/post', 'Created at'),
            'updated_at' => Yii::t('simialbi/bulletin/model/post', 'Updated at')
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
     * Get associated topic
     * @return \yii\db\ActiveQuery
     */
    public function getTopic(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Topic::class, ['id' => 'topic_id']);
    }

    /**
     * Get associated topic via topic
     * @return \yii\db\ActiveQuery
     */
    public function getCategory(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id'])
            ->via('topic');
    }

    /**
     * Get associated attachments
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Attachment::class, ['post_id' => 'id']);
    }
}
