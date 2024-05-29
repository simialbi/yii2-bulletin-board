<?php

use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $voting \simialbi\yii2\bulletin\models\Voting */
/** @var $userAnswers \simialbi\yii2\bulletin\models\VotingUserAnswer[] */
/** @var $boardId int */

?>
<?= Yii::t('simialbi/bulletin', 'User {user} voted in topic {topic}', [
    'user' => $userAnswers[0]->user->name,
    'topic' => $voting->topic->title
]); ?>

<?= $voting->question; ?>
<?php foreach ($userAnswers as $answer): ?>
    * <?= $answer->answer->answer; ?>
<?php endforeach; ?>

<?= Url::to(['voting/view', 'id' => $voting->topic_id, 'boardId' => $boardId], 'https'); ?>