<?php

/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Topic */
/** @var $index int */
/** @var $widget \yii\widgets\ListView */
/** @var $board \simialbi\yii2\bulletin\models\Board */

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Url;

?>

<div class="row">
    <div class="col-3 col-md-1">
        <?php if ($model->author->image): ?>
            <img src="<?= $model->author->image; ?>" class="img-fluid" alt="<?= $model->author->name; ?>">
        <?php endif; ?>
    </div>
    <div class="col-9 col-md-11">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="my-1">
                <?php if ($model->has_voting): ?>
                    <?= FAS::i('ballot-check'); ?>
                <?php endif; ?>
                <?= $model->title; ?>
            </h5>
            <small><?= Yii::$app->formatter->asRelativeTime($model->created_at); ?></small>
        </div>
        <div class="d-flex w-100 justify-content-between">
            <small><?= $model->author->name; ?></small>
            <span class="badge bg-primary rounded-pill">
                <?= Yii::$app->formatter->asInteger($model->getPosts()->count('id')); ?>
            </span>
        </div>
        <div class="topic-actions">
            <?php if (Yii::$app->user->can('bulletinUpdateTopic', ['topic' => $model])): ?>
                <a href="<?= Url::to(['topic/update', 'id' => $model->id, 'boardId' => $board->id]);?>"
                   data-pjax="0" class="position-relative text-decoration-none mr-1 me-1" style="z-index: 2;">
                    <?= FAS::i('pencil') ?>
                </a>
            <?php endif; ?>
            <?php if (Yii::$app->user->can('bulletinDeleteTopic', ['topic' => $model])): ?>
                <a href="<?= Url::to(['topic/delete', 'id' => $model->id, 'boardId' => $board->id]); ?>"
                   data-pjax="0" class="position-relative text-decoration-none text-danger" style="z-index: 2;" data-method="post"
                   data-confirm="<?= Yii::t('yii', 'Are you sure you want to delete this item?'); ?>">
                    <?= FAS::i('trash-alt'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<a href="<?=Url::to(['topic/view', 'id' => $model->id, 'boardId' => $board->id])?>" class="stretched-link"></a>
