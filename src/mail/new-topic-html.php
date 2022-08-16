<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $topic \simialbi\yii2\bulletin\models\Topic */
/** @var $post \simialbi\yii2\bulletin\models\Post */
/** @var $boardId int */

?>

<div class="new-topic-mail" style="text-align: center;">
    <h1><?= Yii::t('simialbi/bulletin', 'New topic created'); ?></h1>

    <h2><?= Html::encode($topic->title); ?></h2>
    <div class="post-text"><?= $post->text; ?></div>
    <a href="<?= Url::to(['topic/view', 'id' => $topic->id, 'boardId' => $boardId], 'https'); ?>"
       style="background-color: navy; color: white; padding: 5px 10px;">
        <?= Html::encode(Yii::t('simialbi/bulletin', 'To the topic')); ?> &rarr;
    </a>
</div>
