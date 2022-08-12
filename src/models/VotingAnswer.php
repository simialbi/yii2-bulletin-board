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
 * @property int $voting_id
 * @property string $answer
 * @property int|string $created_by
 * @property int|string $updated_by
 * @property int|string|\DateTimeInterface $created_at
 * @property int|string|\DateTimeInterface $updated_at
 *
 * @property-read UserInterface $author
 * @property-read UserInterface $updater
 * @property-read Voting $voting
 * @property-read Topic $topic
 */
class VotingAnswer extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%bulletin__voting_answer}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'voting_id'], 'integer'],
            ['answer', 'string'],

            [['voting_id', 'answer'], 'required']
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
            'id' => Yii::t('simialbi/bulletin/model/voting-answer', 'Id'),
            'voting_id' => Yii::t('simialbi/bulletin/model/voting-answer', 'voting'),
            'answer' => Yii::t('simialbi/bulletin/model/voting-answer', 'Answer'),
            'created_by' => Yii::t('simialbi/bulletin/model/voting-answer', 'Created by'),
            'updated_by' => Yii::t('simialbi/bulletin/model/voting-answer', 'Updated by'),
            'created_at' => Yii::t('simialbi/bulletin/model/voting-answer', 'Created at'),
            'updated_at' => Yii::t('simialbi/bulletin/model/voting-answer', 'Updated at')
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
     * Get associated voting
     * @return \yii\db\ActiveQuery
     */
    public function getVoting(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Voting::class, ['id' => 'voting_id']);
    }

    /**
     * Get associated topic via voting
     * @return \yii\db\ActiveQuery
     */
    public function getTopic(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Topic::class, ['id' => 'topic_id'])
            ->via('voting');
    }
}
