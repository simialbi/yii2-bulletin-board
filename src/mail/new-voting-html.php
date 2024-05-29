<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $voting \simialbi\yii2\bulletin\models\Voting */
/** @var $userAnswers \simialbi\yii2\bulletin\models\VotingUserAnswer[] */
/** @var $boardId int */

?>

<div class="new-voting-mail" style="text-align: center;">
    <h1>
        <?= Html::encode(Yii::t('simialbi/bulletin', 'User {user} voted in topic {topic}', [
            'user' => $userAnswers[0]->user->name,
            'topic' => $voting->topic->title
        ])); ?>
    </h1>

    <h2><?= Html::encode($voting->question); ?></h2>
    <div class="voting-results">
        <ul>
            <?php foreach ($userAnswers as $answer): ?>
                <li><?= Html::encode($answer->answer->answer); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <a href="<?= Url::to(['voting/view', 'id' => $voting->topic_id, 'boardId' => $boardId], 'https'); ?>"
       style="background-color: navy; color: white; padding: 5px 10px;">
        <?= Html::encode(Yii::t('simialbi/bulletin', 'To the results')); ?> &rarr;
    </a>
</div>
