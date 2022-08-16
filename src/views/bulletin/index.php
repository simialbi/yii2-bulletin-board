<?php


use rmrevin\yii\fontawesome\FAS;
use simialbi\yii2\bulletin\models\Topic;
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
/** @var $navigation array */
/** @var $board \simialbi\yii2\bulletin\models\Board|null */
/** @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('simialbi/bulletin', 'Overview');
$this->params['breadcrumbs'] = [
    $this->title
];
?>

<div class="sa-bulletin-bulletin-index">
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
            <?php Pjax::begin([
                'id' => 'bulletin-content-pjax'
            ]); ?>
                <?php if ($board): ?>
                    <div class="card">
                        <div class="card-header d-flex justify-content-around align-items-center">
                            <h4 class="card-title m-0"><?= $board->title; ?></h4>
                            <a type="button" href="<?= Url::to(['topic/create', 'boardId' => $board->id]); ?>"
                               class="btn btn-primary ml-auto ms-auto" data-pjax="0">
                                <?= FAS::i('plus'); ?>
                                <span class="d-none d-lg-inline-block">
                                    <?= Yii::t('simialbi/bulletin', 'Create topic'); ?>
                                </span>
                            </a>
                        </div>
                        <?= ListView::widget([
                            'dataProvider' => $dataProvider,
                            'layout' => "<div class=\"card-body border-bottom py-2\">{summary}</div><div class=\"card-body p-0\"><div class=\"list-group list-group-flush\">{items}</div></div><div class=\"card-footer\">{pager}</div>",
                            'itemView' => '_topic-item',
                            'viewParams' => [
                                'board' => $board
                            ],
                            'pager' => [
                                'class' => $pagerClass,
                                'hideOnSinglePage' => false,
                                'listOptions' => ['class' => ['pagination', 'mb-0']]
                            ],
                            'itemOptions' => function (Topic $model, $key, int $index) use ($board) {
                                $class = ['list-group-item', 'list-group-item-action', 'position-relative'];
                                if ($index % 2 !== 0) {
                                    $class[] = 'bg-light';
                                }

                                return [
                                    'tag' => 'div',
                                    'class' => $class
                                ];
                            },
                            'emptyTextOptions' => [
                                'class' => ['card-body']
                            ]
                        ]); ?>
                    </div>
                <?php endif; ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

