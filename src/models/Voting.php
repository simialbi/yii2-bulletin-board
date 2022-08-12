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
 * @property string $question
 * @property bool $multiple_answers_allowed
 * @property int|string $created_by
 * @property int|string $updated_by
 * @property int|string|\DateTimeInterface $created_at
 * @property int|string|\DateTimeInterface $updated_at
 *
 * @property-read UserInterface $author
 * @property-read UserInterface $updater
 * @property-read Topic $topic
 * @property-read VotingAnswer[] $answers
 * @property-read VotingUserAnswer[] $userAnswers
 */
class Voting extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%bulletin__voting}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'topic_id'], 'integer'],
            ['question', 'string'],
            ['multiple_answers_allowed', 'boolean'],

            ['multiple_answers_allowed', 'default', 'value' => false],

            [['topic_id', 'question', 'multiple_answers_allowed'], 'required']
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
            'id' => Yii::t('simialbi/bulletin/model/voting', 'Id'),
            'topic_id' => Yii::t('simialbi/bulletin/model/voting', 'Topic'),
            'question' => Yii::t('simialbi/bulletin/model/voting', 'Question'),
            'multiple_answers_allowed' => Yii::t('simialbi/bulletin/model/voting', 'Multiple answers'),
            'created_by' => Yii::t('simialbi/bulletin/model/voting', 'Created by'),
            'updated_by' => Yii::t('simialbi/bulletin/model/voting', 'Updated by'),
            'created_at' => Yii::t('simialbi/bulletin/model/voting', 'Created at'),
            'updated_at' => Yii::t('simialbi/bulletin/model/voting', 'Updated at')
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
     * Get associated answers
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(VotingAnswer::class, ['voting_id' => 'id']);
    }

    /**
     * Get associated user answers via answers
     * @return \yii\db\ActiveQuery
     */
    public function getUserAnswers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(VotingUserAnswer::class, ['answer_id' => 'id'])
            ->via('answers');
    }
}
