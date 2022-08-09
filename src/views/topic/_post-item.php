<?php

/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Post */
/** @var $index int */
/** @var $widget \yii\widgets\ListView */

?>

<div class="row">
    <div class="col-3 col-md-1">
        <?php if ($model->author->image): ?>
            <img src="<?= $model->author->image; ?>" class="img-fluid" alt="<?= $model->author->name; ?>">
        <?php endif; ?>
    </div>
    <div class="col-9 col-md-11">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="my-1"><?= $model->title; ?></h5>
            <small><?= Yii::$app->formatter->asRelativeTime($model->created_at); ?></small>
        </div>
        <?php if ($index === 0): ?>
        <div class="d-flex w-100 justify-content-between">
            <small><?= $model->author->name; ?></small>
            <div>
                <?php foreach ($model->topic->boards as $board): ?>
                    <span class="badge bg-dark"><?= $board->title; ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <small><?= $model->author->name; ?></small>
        <?php endif; ?>
        <div class="post-content mt-3">
            <?= $model->text; ?>
        </div>
        <?php if ($index === 0): ?>
            <?php foreach ($model->topic->categories as $category): ?>
                <span class="badge bg-light text-body"><?= $category->title; ?></span>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
