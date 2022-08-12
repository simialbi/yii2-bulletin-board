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
 * @property int $answer_id
 * @property int|string $user_id
 * @property int|string|\DateTimeInterface $created_at
 *
 * @property-read UserInterface $user
 * @property-read VotingAnswer $answer
 * @property-read Voting $voting
 * @property-read Topic $topic
 */
class VotingUserAnswer extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return '{{%bulletin__voting_user_answer}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'answer_id'], 'integer'],
            ['user_id', 'string'],

            [['answer_id', 'user_id'], 'required']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => '\yii\behaviors\TimestampBehavior',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'created_at',
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
            'id' => Yii::t('simialbi/bulletin/model/voting-user-answer', 'Id'),
            'answer_id' => Yii::t('simialbi/bulletin/model/voting-user-answer', 'Answer'),
            'user_id' => Yii::t('simialbi/bulletin/model/voting-user-answer', 'User'),
            'created_at' => Yii::t('simialbi/bulletin/model/voting-user-answer', 'Created at')
        ];
    }

    /**
     * Get user
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return call_user_func([Yii::$app->user->identityClass, 'findIdentity'], $this->user_id);
    }

    /**
     * Get associated Answer
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer(): \yii\db\ActiveQuery
    {
        return $this->hasOne(VotingAnswer::class, ['id' => 'answer_id']);
    }

    /**
     * Get associated voting via Answer
     * @return \yii\db\ActiveQuery
     */
    public function getVoting(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Voting::class, ['id' => 'voting_id'])
            ->via('answer');
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
