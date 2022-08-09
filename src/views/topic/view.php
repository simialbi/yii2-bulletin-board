<?php

use rmrevin\yii\fontawesome\FAS;
use simialbi\yii2\bulletin\models\Post;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$navClass = '\yii\widgets\Menu';
$pagerClass = '\yii\widgets\LinkPager';
if (class_exists('\yii\bootstrap4\Nav')) {
    $navClass = '\yii\bootstrap4\Nav';
    $pagerClass = '\yii\bootstrap4\LinkPager';
} elseif (class_exists('\yii\bootstrap5\Nav')) {
    $navClass = '\yii\bootstrap5\Nav';
    $pagerClass = '\yii\bootstrap5\LinkPager';
}

/** @var $this \yii\web\View */
/** @var $topic \simialbi\yii2\bulletin\models\Topic */
/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $navigation array */
/** @var $board \simialbi\yii2\bulletin\models\Board */

if (!Yii::$app->request->isAjax):
    ?>
<div class="row">
    <div class="col-2 col-sm-4">
        <div class="card">
            <div class="card-body">
                <?= $navClass::widget([
                    'items' => $navigation,
                    'options' => [
                        'class' => ['flex-column', 'nav-pills']
                    ],
                    'encodeLabels' => false
                ]); ?>
            </div>
        </div>
    </div>
    <div class="col-10 col-sm-8">
<?php
endif;

Pjax::begin([
    'id' => 'bulletin-content-pjax'
]);

?>
    <div class="card">
        <div class="card-header d-flex justify-content-around align-items-center">
            <a type="button" href="<?= Url::to(['bulletin/index', 'id' => $board->id]) ?>"
               class="btn btn-primary mr-1 me-1">
                <?= FAS::i('arrow-left'); ?>
            </a>
            <h4 class="card-title m-0"><?= $topic->title; ?></h4>
            <a type="button" href="<?= Url::to(['post/create', 'topicId' => $topic->id, 'boardId' => $board->id]); ?>"
               class="btn btn-primary ml-auto ms-auto" data-pjax="0">
                <?= FAS::i('plus') ?>
            </a>
        </div>
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "<div class=\"card-body border-bottom py-2\">{summary}</div><div class=\"card-body p-0\"><div class=\"list-group list-group-flush\">{items}</div></div><div class=\"card-footer\">{pager}</div>",
            'itemView' => '_post-item',
            'pager' => [
                'class' => $pagerClass,
                'hideOnSinglePage' => false,
                'listOptions' => ['class' => ['pagination', 'mb-0']]
            ],
            'itemOptions' => function (Post $model, int $index) {
                $class = ['list-group-item'];
                if ($index % 2 === 0) {
                    $class[] = 'bg-light';
                }

                return [
                    'class' => $class
                ];
            },
            'emptyTextOptions' => [
                'class' => ['card-body']
            ]
        ]); ?>
    </div>

<?php
Pjax::end();

if (!Yii::$app->request->isAjax):
    ?>
    </div>
</div>
<?php
endif;
