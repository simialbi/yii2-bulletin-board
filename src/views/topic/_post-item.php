<?php

/** @var $this \yii\web\View */
/** @var $model \simialbi\yii2\bulletin\models\Post */
/** @var $index int */
/** @var $widget \yii\widgets\ListView */
/** @var $page int */
/** @var $boardId int */
/** @var $noCite bool */

use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="row">
    <div class="col-3 col-md-1">
        <?php if ($model->author->image): ?>
            <img src="<?= $model->author->image; ?>" class="img-fluid" alt="<?= $model->author->name; ?>">
        <?php endif; ?>
    </div>
    <div class="col-9 col-md-11">
        <div class="d-flex w-100">
            <h5 class="my-1"><?= $model->title; ?></h5>
            <small class="ml-auto ms-auto"><?= Yii::$app->formatter->asRelativeTime($model->created_at); ?></small>
            <?php if (!$noCite): ?>
                <a href="<?= Url::to(['post/create', 'postId' => $model->id, 'topicId' => $model->topic_id, 'boardId' => $boardId]); ?>"
                   class="ml-2 ms-2" data-bs-toggle="tooltip" id="tooltip-<?= $model->id; ?>"
                   data-bs-title="<?= Yii::t('simialbi/bulletin', 'Quote reply'); ?>">
                    <?= FAS::i('comment-quote'); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if ($index === 0 && $page == 1): ?>
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
        <div class="post-actions">
            <?php if (Yii::$app->user->can('bulletinUpdatePost', ['post' => $model]) && !$noCite): ?>
                <a href="<?= Url::to(['post/update', 'id' => $model->id, 'boardId' => $boardId]);?>"
                   data-pjax="0" class="text-decoration-none mr-1 me-1" >
                    <?= FAS::i('pencil') ?>
                </a>
            <?php endif; ?>
            <?php if (Yii::$app->user->can('bulletinDeletePost', ['post' => $model]) && ($index !== 0 || $page != 1) && !$noCite): ?>
                <a href="<?= Url::to(['post/delete', 'id' => $model->id, 'boardId' => $boardId]); ?>"
                   data-pjax="0" class="text-decoration-none text-danger" data-method="post"
                   data-confirm="<?= Yii::t('yii', 'Are you sure you want to delete this item?'); ?>">
                    <?= FAS::i('trash-alt'); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if ($model->cite_id && !$noCite): ?>
            <?php $bg = ($index % 2 === 0) ? 'bg-light' : 'bg-white'; ?>
            <div class="post-cite my-3 border-left border-start border-4 border-dark p-3 <?= $bg; ?>">
                <?= $this->render('_post-item', [
                    'boardId' => $boardId,
                    'index' => 100,
                    'model' => $model->citedPost,
                    'page' => $page,
                    'noCite' => true
                ]); ?>
            </div>
        <?php endif; ?>
        <div class="post-content my-3">
            <?= $model->text; ?>
        </div>
        <?php if ($index === 0 && $page == 1 && !empty($model->topic->categories)): ?>
            <div class="post-categories my-3">
                <?php foreach ($model->topic->categories as $category): ?>
                    <span class="badge bg-light text-body"><?= $category->title; ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($model->attachments)): ?>
            <div class="post-attachments my-3">
                <?php foreach ($model->attachments as $attachment): ?>
                    <?= Html::a($attachment->name, $attachment->path, [
                        'class' => [
                            'mr-2',
                            'me-2',
                            'py-1',
                            'px-2',
                            'rounded-pill',
                            'border',
                            'border-dark',
                            'text-reset',
                            'text-nowrap',
                            'd-inline-block',
                            'mw-100',
                            'overflow-hidden'
                        ],
                        'data' => [
                            'pjax' => '0'
                        ],
                        'target' => '_blank'
                    ]); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$js = <<<JS
jQuery('#tooltip-{$model->id}').tooltip();
JS;
$this->registerJs($js);
