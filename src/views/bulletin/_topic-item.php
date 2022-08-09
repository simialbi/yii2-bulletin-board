<?php

/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Topic */
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
        <div class="d-flex w-100 justify-content-between">
            <small><?= $model->author->name; ?></small>
            <span class="badge bg-primary rounded-pill">
                <?= Yii::$app->formatter->asInteger($model->getPosts()->count('id') - 1); ?>
            </span>
        </div>
    </div>
</div>
