<?php

use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $topic \simialbi\yii2\bulletin\models\Topic */
/** @var $post \simialbi\yii2\bulletin\models\Post */
/** @var $boardId int */

?>
<?= Yii::t('simialbi/bulletin', 'New post created'); ?>

<?= $post->title; ?>

<?= $post->text; ?>

<?= Url::to(['topic/view', 'id' => $topic->id, 'boardId' => $boardId], 'https'); ?>
